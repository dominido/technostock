<?php
class Brander_AdminForms_Model_Varien_Image_Adapter_Gd2 extends Varien_Image_Adapter_Gd2
{
    /**
    * Change the image size down to a best match then crop from the center
    *
    * @param int $frameWidth
    * @param int $frameHeight
    */
    public function adaptiveResize($frameWidth = null, $frameHeight = null)
    {
        if (empty($frameWidth) && empty($frameHeight)) {
            throw new Exception('Invalid image dimensions.');
        }
        
        $widthDistance = $this->_imageSrcWidth - $frameWidth;
        $heightDistance = $this->_imageSrcHeight - $frameHeight;

        if (($frameWidth / $frameHeight) > ($this->_imageSrcWidth / $this->_imageSrcHeight)) {
          $this->resize($frameWidth, null);
        } else {
            $this->resize(null, $frameHeight);
        }

        $cropX = 0;
        $cropY = 0;
        
        if ($this->_imageSrcWidth > $frameWidth) {
          $cropX = intval(($this->_imageSrcWidth - $frameWidth) / 2);
        } elseif ($this->_imageSrcHeight > $frameHeight) {
          $cropY = intval(($this->_imageSrcHeight - $frameHeight) / 2);
        }
        
        $isAlpha     = false;
        $isTrueColor = false;
        $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha, $isTrueColor);
        
        if ($isTrueColor) {
          $newImage = imagecreatetruecolor($frameWidth, $frameHeight);
        } else {
          $newImage = imagecreate($frameWidth, $frameHeight);
        }
        
        $this->_fillBackgroundColor($newImage);
     
        imagecopyresampled($newImage, $this->_imageHandler, 0, 0, $cropX, $cropY, $frameWidth, $frameHeight, $frameWidth, $frameHeight);
        $this->_imageHandler = $newImage;
        $this->refreshImageDimensions();
    }

    private function _getTransparency($imageResource, $fileType, &$isAlpha = false, &$isTrueColor = false)
    {
        $isAlpha     = false;
        $isTrueColor = false;
        // assume that transparency is supported by gif/png only
        if ((IMAGETYPE_GIF === $fileType) || (IMAGETYPE_PNG === $fileType)) {
            // check for specific transparent color
            $transparentIndex = imagecolortransparent($imageResource);
            if ($transparentIndex >= 0) {
                return $transparentIndex;
            }
            // assume that truecolor PNG has transparency
            elseif (IMAGETYPE_PNG === $fileType) {
                $isAlpha     = $this->checkAlpha($this->_fileName);
                $isTrueColor = true;
                return $transparentIndex; // -1
            }
        }
        if (IMAGETYPE_JPEG === $fileType) {
            $isTrueColor = true;
        }
        return false;
    }

    private function _fillBackgroundColor(&$imageResourceTo)
    {
        // try to keep transparency, if any
        if ($this->_keepTransparency) {
            $isAlpha = false;
            $transparentIndex = $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha);
            try {
                // fill truecolor png with alpha transparency
                if ($isAlpha) {

                    if (!imagealphablending($imageResourceTo, false)) {
                        throw new Exception('Failed to set alpha blending for PNG image.');
                    }
                    $transparentAlphaColor = imagecolorallocatealpha($imageResourceTo, 0, 0, 0, 127);
                    if (false === $transparentAlphaColor) {
                        throw new Exception('Failed to allocate alpha transparency for PNG image.');
                    }
                    if (!imagefill($imageResourceTo, 0, 0, $transparentAlphaColor)) {
                        throw new Exception('Failed to fill PNG image with alpha transparency.');
                    }
                    if (!imagesavealpha($imageResourceTo, true)) {
                        throw new Exception('Failed to save alpha transparency into PNG image.');
                    }

                    return $transparentAlphaColor;
                }
                // fill image with indexed non-alpha transparency
                elseif (false !== $transparentIndex) {
                    list($r, $g, $b)  = array_values(imagecolorsforindex($this->_imageHandler, $transparentIndex));
                    $transparentColor = imagecolorallocate($imageResourceTo, $r, $g, $b);
                    if (false === $transparentColor) {
                        throw new Exception('Failed to allocate transparent color for image.');
                    }
                    if (!imagefill($imageResourceTo, 0, 0, $transparentColor)) {
                        throw new Exception('Failed to fill image with transparency.');
                    }
                    imagecolortransparent($imageResourceTo, $transparentColor);
                    return $transparentColor;
                }
            }
            catch (Exception $e) {
                Mage::throwException($e->getMessage());
                // fallback to default background color
            }
        }
        list($r, $g, $b) = $this->_backgroundColor;
        $color = imagecolorallocate($imageResourceTo, $r, $g, $b);
        
        if (!imagefill($imageResourceTo, 0, 0, $color)) {
            throw new Exception("Failed to fill image background with color {$r} {$g} {$b}.");
        }

        return $color;
    }

    private function refreshImageDimensions()
    {
        $this->_imageSrcWidth = imagesx($this->_imageHandler);
        $this->_imageSrcHeight = imagesy($this->_imageHandler);
    }

    /**
     * Obtain function name, basing on image type and callback type
     *
     * @param string $callbackType
     * @param int $fileType
     * @return string
     * @throws Exception
     */
    private function _getCallback($callbackType, $fileType = null, $unsupportedText = 'Unsupported image format.')
    {
        if (null === $fileType) {
            $fileType = $this->_fileType;
        }
        if (empty(self::$_callbacks[$fileType])) {
            throw new Exception($unsupportedText);
        }
        if (empty(self::$_callbacks[$fileType][$callbackType])) {
            throw new Exception('Callback not found.');
        }
        return self::$_callbacks[$fileType][$callbackType];
    }

}
