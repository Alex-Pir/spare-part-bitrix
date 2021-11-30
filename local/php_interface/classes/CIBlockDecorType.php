<?
  class CIBlockDecorType{
    public function GetUserTypeDescription() {
      return array(
        "PROPERTY_TYPE"        => "S",
        "USER_TYPE"            => "DSPDECORTYPE",
        "DESCRIPTION"          => "1dsp Тип декора",
        "GetPropertyFieldHtml" => array("CIBlockDecorType", "GetPropertyFieldHtml"),
       );
    }
  
    /*--------- вывод поля свойства на странице редактирования ---------*/
    public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {
      return '<input type="text" name="'.$strHTMLControlName["VALUE"].'" value="'.$value['VALUE'].'" disabled placeholder="Заполнится при выборе декора">';
    }
  }
 ?>