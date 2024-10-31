<?
class UserTable
{
	public static function userFieldValue(int $id)
	{
		$UserFieldAr=false;
		$UserField = CUserFieldEnum::GetList(array(), array("USER_FIELD_ID" => $id));
		while($x = $UserField->GetNext())
		{
			//print_r($v);
			$UserFieldAr[$x["ID"]]=$x["VALUE"];
		}
		return $UserFieldAr;
	}
	public static function getUserFields()
	{
		$userfields = [];
		$rsUserFields = \Bitrix\Main\UserFieldTable::getList(array(
			'filter'=>array("ENTITY_ID" => "USER")
		));
		while($arUserField=$rsUserFields->fetch())
		{
			$ar_res = CUserTypeEntity::GetByID($arUserField["ID"]);
			$userfields[$arUserField["FIELD_NAME"]]=["LABEL_NAME"=>$ar_res["EDIT_FORM_LABEL"]["ru"],"TYPE"=>$arUserField["USER_TYPE_ID"]];    
			if($arUserField["USER_TYPE_ID"]=='enumeration')
			{
				$userfields[$arUserField["ID"]]["VALUES"]=self::userFieldValue($arUserField["ID"]);
			}
		}
		return $userfields;
	}
    public static function getMap()
    {
       
    }
}?>