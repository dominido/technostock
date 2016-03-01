For Magento Enterprise need add to config.xml:

<frontend>
    ...
    <cache>
        <requests>
            <cmsadvanced>enterprise_pagecache/processor_default</cmsadvanced>
        </requests>
    </cache>
</frontend>