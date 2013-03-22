/*global tinyMCE*/
tinyMCE.init({
    mode : "exact",
    elements : "tinyMCE-fr_FR,tinyMCE-de_DE,tinyMCE-en_US",
    theme : "advanced",
    force_br_newlines : true,
    force_p_newlines : false,
    forced_root_block : "",

    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_disable : "styleselect",
    plugins : "table, fullscreen",
    theme_advanced_buttons3_add : "tablecontrols, fullscreen",
    theme_advanced_buttons1_add : "forecolor,backcolor",
    
    width : "640",
    height: "480"


});
