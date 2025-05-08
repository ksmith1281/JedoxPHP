

function arraytostring($array)
{
    return '{' . implode(',', $array) . '}';
}
  

function getchildren($SDB, $DIM, $element)
{
    $elementarray = array_column(application()->PALO_ELEMENT_LIST_CHILDREN($SDB, $DIM, $element), 0);

    return($elementarray);
}

function getconsolidated($SDB, $DIM, $element)
{
    $elementarray = array_column(application()->PALO_ELEMENT_LIST_CHILDREN($SDB, $DIM, $element), 2);
	$resultArray = array_map(function ($value) 
		{
        	return ($value === 'consolidated') ? '1' : '0';
    	}, $elementarray);

    return($resultArray);
    //return($elementarray);
}
function isconsolidated($SDB, $DIM, $element)
{
  $result = application()->PALO_ETYPE($SDB, $DIM, $element);
  return ($result === 'consolidated') ? 1 : 0;
}

function getbaseelements($sdb,$dim,$element) 
{
  	// Timer condtions
	$start_time = microtime(true);
	$max_exec_time = 5; // in seconds
  	$element_array[0] = $element;
  	$element_level[0] = isconsolidated($sdb,$dim,$element);
  	$base_element_array =[];
  
  	if ($element_level[0] == 0) {$base_element_array = $element_array;}
  
	$all_children = []; 
  	$all_consolid = [];
  	$children_build = [];
  	$consolid_build =[];
  	//$consolid_build = 0;
  	$j=0;
  	
  	while (array_sum($element_level)!==0)
	{
  		$array_count = count($element_array);
  		for ($i = 0; $i < $array_count; $i++)
		{
	  	
			if ($element_level[$i] == 0) 
			{
				//$base_element_array[$j] = $element_array[$i];
			  	$children_build[] = $element_array[$i];
		  		$consolid_build[] = 0;
			  	//$j++;
			}
	  		else
			{
			  	$children_build = getchildren($sdb,$dim,$element_array[$i]);
			  	$consolid_build = getconsolidated($sdb,$dim,$element_array[$i]);
			} 	
	 	 	$all_children = array_merge($all_children,$children_build);
		  	$all_consolid = array_merge($all_consolid,$consolid_build);
		  	$children_build = [];
  			$consolid_build =[];
		}

  		$element_array = $all_children;
  		$element_level = $all_consolid;
  		$children_build = [];
  		$consolid_build =[];
  		$all_children = []; 
  		$all_consolid = [];
//		if ($j == 1) 
//		{
//		activeworkbook()->sheets('Backend')->range('P20')->value=$j;
//	    activeworkbook()->sheets('Backend')->range('P21')->value=arraytostring($element_array);
//		activeworkbook()->sheets('Backend')->range('P22')->value=arraytostring($element_level);
//		}
	  $j++;
	//Timer for break condition
	$current_time = microtime(true);
	$elapsed_time = $current_time-$start_time;
	if ($elapsed_time > $max_exec_time) 
		{ 
			return __msgbox('Timed Out (Max time: '.round($elapsed_time,2).' sec)'); break;
		}
	}  

  
  	return $element_array;
}

function getperlexp($array)
{
  	
  	return '~|' . implode('|', $array);
}

function buildperlexp()
{
  // Start Time
  $start_time = microtime(true);

  // Server/Database
  	
  $sdb = activeworkbook()->sheets('Backend')->Range('C2')->value;
  	
  // Dimensions
  	
  $dim_company 	= activeworkbook()->sheets('Backend')->Range('B9')->value;
  $dim_ddc 		= activeworkbook()->sheets('Backend')->Range('B10')->value;
  $dim_class 	= activeworkbook()->sheets('Backend')->Range('B11')->value;
  $dim_lob 		= activeworkbook()->sheets('Backend')->Range('B12')->value;
  $dim_sublob 	= activeworkbook()->sheets('Backend')->Range('B13')->value;
  $dim_bustype 	= activeworkbook()->sheets('Backend')->Range('B14')->value;
  $dim_loc 		= activeworkbook()->sheets('Backend')->Range('B15')->value;
	
  // Elements

  $element_company[0] 	= activeworkbook()->sheets('Backend')->Range('C9')->value;
  $element_ddc[0] 		= activeworkbook()->sheets('Backend')->Range('C10')->value;
  $element_class[0] 	= activeworkbook()->sheets('Backend')->Range('C11')->value;
  $element_lob[0] 		= activeworkbook()->sheets('Backend')->Range('C12')->value;
  $element_sublob[0] 	= activeworkbook()->sheets('Backend')->Range('C13')->value;
  $element_bustype[0] 	= activeworkbook()->sheets('Backend')->Range('C14')->value;
  $element_loc[0] 		= activeworkbook()->sheets('Backend')->Range('C15')->value;
  	
  //Check if filter box is checked
  $filtercheckbox = activeworkbook()->sheets('Backend')->Range('F19')->value;
  
  if ($filtercheckbox)
  {

    // Initial Consolidation

    $consolid_company 	= isconsolidated($sdb,$dim_company,$element_company[0]);
    $consolid_ddc 		= isconsolidated($sdb,$dim_ddc,$element_ddc[0]);
    $consolid_class 		= isconsolidated($sdb,$dim_class,$element_class[0]);
    $consolid_lob 		= isconsolidated($sdb,$dim_lob,$element_lob[0]);
    $consolid_sublob 		= isconsolidated($sdb,$dim_sublob,$element_sublob[0]);
    $consolid_bustype 	= isconsolidated($sdb,$dim_bustype,$element_bustype[0]);
    $consolid_loc 		= isconsolidated($sdb,$dim_loc,$element_loc[0]);

    // Outputs
  
    
  
  	activeworkbook()->sheets('Backend')->range('F9')->value=getperlexp(getbaseelements($sdb,$dim_company,$element_company[0]));
    activeworkbook()->sheets('Backend')->range('F10')->value=getperlexp(getbaseelements($sdb,$dim_ddc,$element_ddc[0]));
    activeworkbook()->sheets('Backend')->range('F11')->value=getperlexp(getbaseelements($sdb,$dim_class,$element_class[0]));
  	activeworkbook()->sheets('Backend')->range('F12')->value=getperlexp(getbaseelements($sdb,$dim_lob,$element_lob[0]));
  	activeworkbook()->sheets('Backend')->range('F13')->value=getperlexp(getbaseelements($sdb,$dim_sublob,$element_sublob[0]));
  	activeworkbook()->sheets('Backend')->range('F14')->value=getperlexp(getbaseelements($sdb,$dim_bustype,$element_bustype[0]));
  	activeworkbook()->sheets('Backend')->range('F15')->value=getperlexp(getbaseelements($sdb,$dim_loc,$element_loc[0]));
  }
  else 
  {
	activeworkbook()->sheets('Backend')->range('F9')->value="";
    activeworkbook()->sheets('Backend')->range('F10')->value="";
    activeworkbook()->sheets('Backend')->range('F11')->value="";
  	activeworkbook()->sheets('Backend')->range('F12')->value="";
  	activeworkbook()->sheets('Backend')->range('F13')->value="";
  	activeworkbook()->sheets('Backend')->range('F14')->value="";
  	activeworkbook()->sheets('Backend')->range('F15')->value="";
  } 
  
  activeworkbook()->sheets('Backend')->range('E9')->value=arraytostring(getbaseelements($sdb,$dim_company,$element_company[0]));
  activeworkbook()->sheets('Backend')->range('E10')->value=arraytostring(getbaseelements($sdb,$dim_ddc,$element_ddc[0]));
  activeworkbook()->sheets('Backend')->range('E11')->value=arraytostring(getbaseelements($sdb,$dim_class,$element_class[0]));
  activeworkbook()->sheets('Backend')->range('E12')->value=arraytostring(getbaseelements($sdb,$dim_lob,$element_lob[0]));
  activeworkbook()->sheets('Backend')->range('E13')->value=arraytostring(getbaseelements($sdb,$dim_sublob,$element_sublob[0]));
  activeworkbook()->sheets('Backend')->range('E14')->value=arraytostring(getbaseelements($sdb,$dim_bustype,$element_bustype[0]));
  activeworkbook()->sheets('Backend')->range('E15')->value=arraytostring(getbaseelements($sdb,$dim_loc,$element_loc[0]));
  
  //End Time
  $current_time = microtime(true);
  $elapsed_time = $current_time-$start_time;
  activeworkbook()->sheets('Backend')->range('F18')->value=round($elapsed_time,2).' sec';
}
