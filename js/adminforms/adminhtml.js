var adminFormsBlockTemplateSyntax = /(^|.|\r|\n)({{(\w+)}})/; 
function setSettings(urlTemplate, setElement) {
 var template = new Template(urlTemplate, adminFormsBlockTemplateSyntax);
 setLocation(template.evaluate({attribute_set:$F(setElement)}));
} 