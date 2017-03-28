<?php

		
	class Racer{
			public $driver_reg;
			public $car_no;
			public $name;
			public $team;
			public $best_time;
			public $best_time_ddd;
			public $last_time;
			public $last_time_ddd;
			public $laps;
			public $total_time;
			public $total_time_ddd;
			function __construct($driver_reg_,$car_no_,$name_,$team_,$best_time_,$best_time_ddd_,$last_time_,$last_time_ddd_,$laps_,$total_time_,$total_time_ddd_){
	                	$driver_reg=$driver_reg_;
                        	$car_no=$car_no_;
				$name=$name_;
                        	$team=$team_;
                        	$best_time=$best_time_;
                        	$best_time_ddd=$best_time_ddd_;
                        	$last_time=$last_time_;
                        	$last_time_ddd=$last_time_ddd_;
                        	$laps=$laps_;
                        	$total_time=$total_time_;
                        	$total_time_ddd=$total_time_ddd_;
		}
	}
	function get_data_from($database_name,$datatable_name)
	{
		$con=mysql_connect("localhost:3306","root","") or die ('Not connected:'.mysql_error());
        	mysql_select_db($database_name,$con);
        	$now_info=mysql_query("SELECT * FROM ".$datatable_name);
        	while($row=mysql_fetch_array($now_info)) $Data[]=$row;
        	mysql_close($con);
		return $Data;
	}
	
	function addobj($record)
	{
		$man=new Racer($record['Driver_Reg'],$record['Car_no'],$record['Driver_name'],$record['Team'],$record['Best_time'],$record['Best_time_DDD'],$record['Last_time'],$record['Last_time_DDD'],$record['Laps'],$record['Total_time'],$record['Total_time_DDD']);
		return $man;
	}
	function cmp($a,$b)
	{
		$tmp1=($a->total_time).($a->total_time_ddd);
		$tmp2=($b->total_time).($b->total_time_ddd);
		return $tmp1<$tmp2?1:-1;
	}
	if(!empty($_GET))
	{
		$response="";
		$races=get_data_from("testcom","now");
		$race_ids=array();
		foreach($races as $record) 
		{
			$race_ids[]=$record['Race_id'];
		}
		$cnt=count($race_ids);
		if(!$cnt) {echo "no races is going on!";exit(1);}
		sort($race_ids);
		$race_ids_unique=array();
		$race_ids_unique[$race_ids[0]]=array();
		for($i=1;$i<count($race_ids);$i++)
		{
			if($race_ids[$i]!=$race_ids[$i-1])
			{
				$race_ids_unique[$race_ids[$i]]=array();
			}
		}
		
		foreach($races as $record)
		{
			$race_ids_unique[$record['Race_id']][]=addobj($record);
		}
		$Data=get_data_from("testcom","race");
		$all_races=array();
		foreach($Data as $record)
		{
			$all_races[$record['Race_id']]=array($record['Race'],$record['Race_Group'],$record['Race_Event'],$record['Circuit_Name'],$record['Circuit_Length'],$record['Race_Run'],$record['Race_Type'],$record['Race_start_Data'],$record['Race_start_Time']);
		}
		foreach($race_ids_unique as $key=>$val)
		{
			usort($race_ids_unique[$key],"cmp");
		}
		
		echo $response;		


	}



?>
