if(window.tinyMceWysiwygSetup)
{
    tinyMceWysiwygSetup.prototype.originalGetSettings = tinyMceWysiwygSetup.prototype.getSettings;
    tinyMceWysiwygSetup.prototype.getSettings = function(mode)
    {
        var settings = this.originalGetSettings(mode);
        settings.language = 'en';
        return settings;
    }        
}
