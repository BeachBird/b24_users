<?
	define("NO_KEEP_STATISTIC", true);
	define("NO_AGENT_STATISTIC", true);
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/csv_data.php");
	require_once("./lib/usertable.php");
	
	$per_page=100;
	
	$fields = UserTable::getUserFields();
	if(isset($_GET["step"]))
		$step=intval($_GET["step"]);
	else
		$step=0;
	$first=true;

	$filePath = $_SERVER["DOCUMENT_ROOT"].'/upload/users.csv';
	$delimiter = ";";  
	$users = \Bitrix\Main\UserTable::getList(array(
		'select' => array('CNT'),
		'runtime' => array(
			new Bitrix\Main\ORM\Fields\ExpressionField('CNT', 'COUNT(*)')
		)
	));
	$count=0;
	if($count=$users->fetch())
		$count=$count["CNT"];
	if($count>$step*$per_page)
	{
		$users = \Bitrix\Main\UserTable::getList(array(
			'filter' => array(),
			'limit'=>$per_page,
			'offset'=>$per_page*$step,
			'select'=>array('*','UF_*')
		));
		while($user=$users->fetch())
		{
			if($step==0&&$first)
			{
				$first=false;
				$list_0=[];
				$list=[];
				foreach($user as $k=>$v)
				{
					if(is_array($v))$v=json_encode($v);
					if(strpos($k,"UF_")===FALSE)
					{
						$list_0[]=$k;
						$list[]=$v;
					}
					else
					{
						$list_0[]=$fields[$k]["LABEL_NAME"];
						$list[]=$v;
					}
				}
				$fp = fopen($filePath, 'w+');
				@fclose($fp);
				$fields_type = 'R'; 
				$csvFile = new \CCSVData($fields_type, false);
				$csvFile->SetFieldsType($fields_type);
				$csvFile->SetDelimiter($delimiter);
				$csvFile->SetFirstHeader(true);
				$csvFile->SaveFile($filePath, $list_0);
				$csvFile->SaveFile($filePath, $list);
			}
			else
			{
				$fields_type = 'R'; 
				$csvFile = new \CCSVData($fields_type, false);
				$csvFile->SetFieldsType($fields_type);
				$csvFile->SetDelimiter($delimiter);
				$csvFile->SetFirstHeader(false);
				$list=[];
				foreach($user as $k=>$v)
				{
					if(is_array($v))$v=json_encode($v);
					$list[]=$v;
				}
				$csvFile->SaveFile($filePath, $list);
			}
		}
		echo "Шаг ".$step;
		header("Location: ".$APPLICATION->GetCurPage()."?step=".++$step);
		exit( );
	}
	else {
		echo "Файл доступен по <a href='/upload/users.csv?v=5'>ссылке</a>";
	}
