Validation.addAllThese(
    [
        [
            'validate-layerednavigation-symbols',
            'Please make sure that value in this field is different from value in field below.',
            function (v) {
                return v != $('layerednavigation_seo_option_char').value;
            }
        ]
    ]
);
