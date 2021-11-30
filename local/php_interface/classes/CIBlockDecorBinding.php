<?
  class CIBlockDecorBinding {
    function GetUserTypeDescription() {
      return array(
        "PROPERTY_TYPE"      =>"E",
        "USER_TYPE"      =>"DSPDECORBINDING",
        "DESCRIPTION"      =>"1dsp Привязка декора",
        "GetPropertyFieldHtml" =>array("CIBlockDecorBinding", "GetPropertyFieldHtml")
      );
    }

    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {
      
    }
  }
?>