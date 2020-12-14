<?php

// output headers so that the file is downloaded rather than displayed
 header('Content-Type: text/csv; charset=utf-8');
 header('Content-Disposition: attachment; filename=deveventuofstaout.csv');

/**
 * Clean up csv EventOn export 
 */

// Allow for more processing power 
ini_set('memory_limit', '1028M');
$mod = 'debug'; // show errors


// Import class library 
$root = str_replace("\\","/",dirname(dirname(__FILE__)));
$file_root = str_replace("\\","/",getcwd() );                // file root directory 
$root = str_replace("\\", "/", dirname(dirname(__FILE__)));  // parent root directory 
require_once 'StringUtil.php';

// allow processing content for html readability
$process_html_content = true;

// --------------------------- [Until functions]   ----------------------------   //   


		function html_process_content($content, $process = true){
			//$content = iconv('UTF-8', 'Windows-1252', $content);
			return ($process)? htmlentities($content, ENT_QUOTES): $content;
		}

/*
 * for debugging encoding issues
*/
function debugChr($str){
    $str = str_split($str);
    $stringUtilObj   = new StringUtil;  
    echo '<ul>';
    foreach($str  as $key=>$item){
        echo '<li>'.$item.': '. $stringUtilObj->clean($item,FILTER_SANITIZE_STRING,FILTER_FLAG_ENCODE_LOW, false,true).'</li>';
    }
    echo '</ul><br><br>';
    // echo $stringUtilObj->clean($str).'<br><br>';
    die();
}


/**
 * Clean data
 */
function cleanStr($str,$notText){
// String utility object for string funcations
$stringUtilObj   = new StringUtil;  
   $clean = addslashes(trim($stringUtilObj->clean(stripslashes($str))));
   // strip out any remaining slashes 
   $clean = str_replace ("\\\'","\'",$clean);
   $clean = str_replace ("\\'","\'",$clean);
   $clean = str_replace ("\\\'s","\'",$clean);
   $clean = str_replace ("\\\\","\\",$clean);
   $clean = preg_replace('/\\\\/', '\\',  $clean);
   $clean = str_replace("    ", "", $clean); 
  
   if ($notText){	
      // extra clean up if not a block of text 
      // if data is not text remove any tabs,., carriage returns 
      $clean = trim(preg_replace('/\s+/', '', $clean));
      $clean = trim(str_replace(' ', '', $clean));
      $clean= str_replace('.', '', $clean); // remove dots
      $clean = str_replace(' ', '', $clean); // remove spaces
      $clean = str_replace("\t", '', $clean); // remove tabs
      $clean = str_replace("\n", '', $clean); // remove new lines
      $clean = str_replace("\r", '', $clean); // remove carriage returns
    }
   return  $clean;
}

/**
 * Checks if valid date
 * @param type $date
 * @param type $format
 * @return type
 */
function validateDate($date, $format = 'Y-m-d')  {
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}

/**
 * Checks if valid time
 * @param type $time_str
 * @param type $format
 * @return type
 */
function isTimeValid($time_str, $format = 'H:i') {
    $DateTime = \DateTime::createFromFormat( "d/m/Y {$format}", "10/10/2010 {$time_str}" );
    return $DateTime && $DateTime->format( "d/m/Y {$format}" ) == "10/10/2010 {$time_str}";
}

/**
 * Checks if value if a number list
 * @param type $lst
 * @return boolean
 */
function isNumberList($lst){
   if(substr($lst, -1) ==',' ){
    $lst = substr($lst, 0, -1);
   }
   $aryList = explode(',',trim($lst));
   foreach($aryList as $i){
    if(!is_numeric(trim($i))){
       // I am not a number, I am a free man 
       return false;
     }
   }
  return true;
}

// ----------------------- get all events
// events
$events = new WP_Query(array(
	'posts_per_page'=>-1,
	'post_type' => 'ajde_events',
	'post_status'=>'any'			
));

	$evo_opt = get_option('evcal_options_evcal_1');
	$event_type_count = evo_get_ett_count($evo_opt);
	$cmd_count = evo_calculate_cmd_count($evo_opt);

	// event types
	$evt = array();
	for($y=1; $y<=$event_type_count;  $y++){
		$_ett_name = ($y==1)? 'event_type': 'event_type_'.$y;
		$evt[$_ett_name]= $_ett_name.'_slug';
	}


   // for event custom meta data
	$cust = array();
	for($z=1; $z<=$cmd_count;  $z++){
		$_cmd_name = 'cmd_'.$z;
		array_push($cust, $_cmd_name);
	}


// --------------------------- [End until functions]   ----------------------------   //  


$ct = 1;                                      // row count (output values)
$row = 1;                                     // row count  (input file)

$csvOutPath = 'deveventuofstaout.csv';  // output cleaned data
$handle = fopen('php://output', 'w');      // open output file 


$na = ''; // not set

    
       $dataOut[0] = 'publish_status';
       $dataOut[1] = 'event_id';
       $dataOut[2] = 'color';
       $dataOut[3] = 'event_name';
       $dataOut[4] = 'event_description';
       $dataOut[5] = 'event_start_date';
       $dataOut[6] = 'event_start_time';
       $dataOut[7] = 'event_end_date';
       $dataOut[8] = 'event_end_time';
       $dataOut[9] = 'all_day';
       $dataOut[10] = 'hide_end_time';
       $dataOut[11] = 'event_gmap';
       $dataOut[12] = 'yearlong';
       $dataOut[13] = 'featured';
       $dataOut[14] = 'evo_location_id';
       $dataOut[15] = 'location_name';
       $dataOut[16] = 'event_location';
       $dataOut[17] = 'location_description';
       $dataOut[18] = 'location_latitude';
       $dataOut[19] = 'location_longitude';
       $dataOut[20] = 'location_link';
       $dataOut[21] = 'location_img';
       $dataOut[22] = 'evo_organizer_id';
       $dataOut[23] = 'event_organizer';
       $dataOut[24] = 'organizer_description';
       $dataOut[25] = 'evcal_org_contact';
       $dataOut[26] = 'evcal_org_address';
       $dataOut[27] = 'evcal_org_exlink';
       $dataOut[28] = 'evo_org_img';       
       $dataOut[29] = 'evcal_subtitle';
       $dataOut[30] = 'learnmore link';
       $dataOut[31] = 'image_url';
       $dataOut[32] = 'repeatevent';
       $dataOut[33] = 'frequency';
       $dataOut[34] = 'repeats';
       $dataOut[35] = 'repeatby';
       $dataOut[36] = 'event_type';
       $dataOut[37] = 'event_type_slug';
       $dataOut[38] = 'event_type_2';
       $dataOut[39] = 'event_type_slug_2';
       $dataOut[40] = 'event_type_3';
       $dataOut[41] = 'event_type_slug_3';
       $dataOut[42] = 'event_type_4';
       $dataOut[43] = 'event_type_slug_4';
       $dataOut[44] = 'cmd_1';
       $dataOut[45] = 'cmd_2';
       $dataOut[46] = 'cmd_3';
       $dataOut[47] = 'cmd_4';
       $dataOut[48] = 'cmd_5';
       $dataOut[49] = 'cmd_6';
       $dataOut[50] = 'cmd_7';
       $dataOut[51] = 'cmd_8';


fputcsv($handle, $dataOut,',');


   // output table header
/*
   echo '<table border="1"><tr>
                  <td>Count</td>
                  <td>1 - publish_status</td>
                  <td>2 - event_id</td>
                  <td>2 - color</td>
                  <td>3 - event_name</td>
                  <td>4 - event_description</td>
                  <td>5 - event_start_date</td>
                  <td>6 - event_start_time</td>
                  <td>7 - event_end_date</td>
                  <td>8 - event_end_time</td>
                  <td>9 - all_day</td>
                  <td>10 - hide_end_time</td>
                  <td>11 - event_gmap</td>
                  <td>12 - yearlong</td>
                  <td>13 - featured</td>
                  <td>14 - evo_location_id</td>
                  <td>15 - location_name</td>
                  <td>16 - event_location</td>
                  <td>17 - location_description</td>
                  <td>18 - location_latitude</td>
                  <td>19 - location_longitude</td>
                  <td>20 - location_link</td>
                  <td>21 - location_img</td>
                  <td>22 - evo_organizer_id</td>
                  <td>23 - event_organizer</td>
                  <td>24 - organizer_description</td>
                  <td>25 - evcal_org_contact</td>
                  <td>26 - evcal_org_address</td>
                  <td>27 - evcal_org_exlink</td>
                  <td>28  -evo_org_img</td>
                  <td>29 - evcal_subtitle</td>
                  <td>30 - learnmore link</td>
                  <td>31 - image_url</td>
                  <td>32 - repeatevent</td>
                  <td>33 - frequency</td>
                  <td>34 - repeats</td>
                  <td>35 - repeatby</td>
                  <td>36 - event_type</td>
                  <td>37 - event_type_slug</td>
                  <td>38 - event_type_2</td>
                  <td>39 - event_type_slug_2</td>
                  <td>40 - event_type_3</td>
                  <td>41 - event_type_slug_3</td>
                  <td>42 - event_type_4</td>
                  <td>43 - event_type_slug_4</td>
                  <td>44 - cmd_1</td>
                  <td>45 - cmd_2</td>
                  <td>46 - cmd_3</td>
                  <td>47 - cmd_4</td>
                  <td>48 - cmd_5</td>
                  <td>49 - cmd_6</td>
                  <td>50 - cmd_7</td>
                  <td>51 - cmd_8</td>
                  <td>ok to save</td>
';

*/
   // for each event
   while($events->have_posts()){
        $events->the_post();


	$__id = get_the_ID();
 $lis = "'".$__id."',";



	$pmv = get_post_meta($__id);
// echo '<tr>';
   
  //  $num = count($data);
    $dataOut = array();    // array to hold cleaned data
    $dataOutStatus = true; // is the data ok to be imported
   // echo "<td>$row</td>";
    

    // ------------  publish_status (publish & draft)-------------//
    if (get_post_status($__id) && (trim(get_post_status($__id)) == 'publish' || trim(get_post_status($__id)) == 'draft')){
       $dataOut[0] = trim(get_post_status($__id));
    }else{
       $dataOut[0] = 'draft';
    }
 //   echo "<td>$dataOut[0]</td>";



    // ------------ event_id must be a string -------------//
    if (is_numeric(trim($__id))){
       $dataOut[1] = trim($__id);
    }else{
       $dataOutStatus = false;
    }
 //   echo "<td>$dataOut[1]</td>";

    // ------------  color  -------------//

    $dataOut[2] =  (!empty($pmv['evcal_event_color'])? $pmv['evcal_event_color'][0]:$na);
 //   echo "<td>$dataOut[2]</td>";



    // ------------   event_name  -------------//
    $eventName = get_the_title();
    $eventName = html_process_content($eventName, $process_html_content);
							
    if (strlen(trim($eventName)) > 0){
       $dataOut[3] = cleanStr(trim($eventName),false);
    }else{
       $dataOutStatus = false;
       $dataOut[3] = $na;
     }
 //    echo "<td>$dataOut[3]</td>";

    // ------------    event_description  -------------//
    $event_content = get_the_content();
    if (strlen(trim($event_content)) > 0){
        $dataOut[4] = cleanStr(trim($event_content),false);
    }else{
        $dataOut[4] = $na;
     }
 //    echo "<td>$dataOut[4]</td>";

    

    $start = (!empty($pmv['evcal_srow'])?$pmv['evcal_srow'][0]:'');
    if(!empty($start)){

// ------------     event_start_date -------------//
$dataOut[5] = (cleanStr(trim(date( apply_filters('evo_csv_export_dateformat','m/d/Y'), $start)),true)?cleanStr(trim(date( apply_filters('evo_csv_export_dateformat','m/d/Y'), $start)),true):$na);

// ------------  event_start_date -------------//
$dataOut[6] = (cleanStr(trim(date( apply_filters('evo_csv_export_timeformat','h:i:A'), $start)),true)?cleanStr(trim(date( apply_filters('evo_csv_export_timeformat','h:i:A'), $start)),true):$na);
		}else{
          $dataOutStatus = false;
      }			

// echo "<td>$dataOut[5]</td>";
// echo "<td>$dataOut[6]</td>";
    $end = (!empty($pmv['evcal_erow'])?$pmv['evcal_erow'][0]:'');
    if(!empty($end)){

// ------------   event_end_date must be date -------------//
$dataOut[7] = (cleanStr(trim(date( apply_filters('evo_csv_export_dateformat','m/d/Y'), $end)),true)?cleanStr(trim(date( apply_filters('evo_csv_export_dateformat','m/d/Y'), $end)),true):$na);

// ------------    event_end_time must be time -------------//
$dataOut[8] = (cleanStr(trim(date( apply_filters('evo_csv_export_timeformat','h:i:A'), $end)),true)?cleanStr(trim(date( apply_filters('evo_csv_export_timeformat','h:i:A'), $end)),true):$na);
		}else{
          $dataOutStatus = false;
      }	

 //   echo "<td>$dataOut[7]</td>";
 //   echo "<td>$dataOut[8]</td>";

    // ------------     all_day  no/yes default to no-------------//
    $dataOut[9] = cleanStr(trim(( (!empty($pmv['evcal_allday']) && $pmv['evcal_allday'][0]=='yes') ? 'yes': 'no')),true);
 //   echo "<td>$dataOut[9]</td>";

    // ------------      hide_end_time no/yes default to no -------------//
    $dataOut[10] =   cleanStr(trim(( (!empty($pmv['evo_hide_endtime']) && $pmv['evo_hide_endtime'][0]=='yes') ? 'yes': 'no')),true);
 //   echo "<td>$dataOut[10]</td>";

    // ------------       event_gmap no/yes default to no -------------//
    $dataOut[11] =   cleanStr(trim(( (!empty($pmv['evcal_gmap_gen']) && $pmv['evcal_gmap_gen'][0]=='yes') ? 'yes': 'no')),true);
 //   echo "<td>$dataOut[11]</td>";

    // ------------       yearlong  no/yes default to no -------------//
     $dataOut[12] =  cleanStr(trim(( (!empty($pmv['evo_year_long']) && $pmv['evo_year_long'][0]=='yes') ? 'yes': 'no')),true);
//     echo "<td>$dataOut[12]</td>";

    // ------------       featured  no/yes default to no -------------//
    $dataOut[13]=  cleanStr(trim(( (!empty($pmv['_featured']) && $pmv['_featured'][0]=='yes') ? 'yes': 'no')),true);
 //   echo "<td>$dataOut[13]</td>";

    // ------------       location - must have an id -------------//
	// location for this event
	$_event_location_term = wp_get_object_terms( $__id, 'event_location' );
	$location_term_meta = $event_location_term_id = false;
	if ( $_event_location_term && ! is_wp_error( $_event_location_term ) ){
		$event_location_term_id = $_event_location_term[0]->term_id;
		$location_term_meta = evo_get_term_meta('event_location', $event_location_term_id,'', true);
	}
    if (strlen(trim($event_location_term_id)) > 0 && (is_numeric(trim($event_location_term_id))) ){


	$locdescription = html_process_content( $_event_location_term[0]->description);
	$locname = html_process_content( $_event_location_term[0]->name, $process_html_content);									
	if(!strlen($locname) > 0 ){
        $locname = html_process_content($pmv['location_name'][0], $process_html_content);
   }

    $location_lat = ($location_term_meta && !empty($location_term_meta['location_lat']) ? $location_term_meta['location_lat'] :$na);										
    $location_lon = ($location_term_meta && !empty($location_term_meta['location_lon']) ?  $location_term_meta['location_lon'] :$na);
    $evcal_location_link = ($location_term_meta && !empty($location_term_meta['evcal_location_link']) ?  $location_term_meta['evcal_location_link']:$na);

   if($location_term_meta){
      $event_location= !empty($location_term_meta['location_address'])? html_process_content($location_term_meta['location_address'], $process_html_content):$na;
						
   }elseif(!empty($pmv['evcal_location']) ){
       $event_location=html_process_content($pmv['evcal_location'][0], $process_html_content);
   }

							
$location_img = ($location_term_meta && !empty($location_term_meta['evo_loc_img'])) ? $location_term_meta['evo_loc_img']:$na;


															
         // has id
        $dataOut[14] = (cleanStr(trim($event_location_term_id),true) ? cleanStr($event_location_term_id,true) : $na);   // evo_location_id
        $dataOut[15] = (cleanStr(trim($locname),false)? cleanStr(trim($locname),false) : $na);                           // location_name
        $dataOut[16] = (cleanStr(trim($event_location),false)? cleanStr(trim($event_location),false) : $na);             // event_location
        $dataOut[17] = (cleanStr(trim($locdescription),false)? cleanStr(trim($locdescription),false) :$na);              // location_description
        $dataOut[18] = (cleanStr(trim($location_lat),false)? cleanStr(trim($location_lat),false) : $na);                 // location_latitude
        $dataOut[19] = (cleanStr(trim($location_lon),false)? cleanStr(trim($location_lon),false) : $na);                 // location_longitude
        $dataOut[20] = (cleanStr(trim($evcal_location_link),false)? cleanStr(trim($evcal_location_link),false) : $na);   // location_link
        $dataOut[21] = (cleanStr(trim($location_img),true)?  cleanStr(trim($location_img),true) : $na);                  // location_img

      
    }else{
         // no id
         $dataOut[14] = $na;
         $dataOut[15] = $na;
         $dataOut[16] = $na;
         $dataOut[17] = $na;
         $dataOut[18] = $na;
         $dataOut[19] = $na;
         $dataOut[20] = $na;
         $dataOut[21] = $na;      

    }
 //   echo "<td>$dataOut[14]</td><td>$dataOut[15]</td><td>$dataOut[16]</td><td>$dataOut[17]</td><td>$dataOut[18]</td><td>$dataOut[19]</td><td>$dataOut[20]</td><td>$dataOut[21]</td>";

    // ------------      organizer - must have an id -------------//

$_event_location_term = wp_get_object_terms( $__id, 'event_location' );
	
	// Organizer for this event
	$_event_organizer_term = wp_get_object_terms( $__id, 'event_organizer' );
	$organizer_term_meta = $organizer_term_id = false;




$t = get_terms( array(
    'hide_empty' => false,
) );


if(!empty($_event_organizer_term)){
$eventSlug = $_event_organizer_term[0]->slug;
}



	$term = get_term_by( 'slug', $eventSlug, 'event_organizer' );

	$org_term_meta = evo_get_term_meta( 'event_organizer',$term->term_id );


	if( $_event_organizer_term && !is_wp_error($_event_organizer_term)){
		$organizer_term_id = $_event_organizer_term[0]->term_id;
		$organizer_term_meta = evo_get_term_meta('event_organizer',$organizer_term_id, true);
	}

									
    if (strlen(trim($organizer_term_id)) > 0 && is_numeric($organizer_term_id)  ){

      	$event_organizer =html_process_content($_event_organizer_term[0]->name, $process_html_content);	
	    if(empty($event_organizer) || $event_organizer == '' ){
	     	$event_organizer = html_process_content($pmv['event_organizer'][0], $process_html_content);
	    }


		$orgdescription = html_process_content($_event_organizer_term[0]->description);
		$org_contact = $org_term_meta['evcal_org_contact'];
		$evcal_org_address =  $org_term_meta['evcal_org_address'];

		$evcal_org_exlink =  $org_term_meta['evcal_org_exlink'];
		$evo_org_img = $org_term_meta['evo_org_img'];
																			
																
       $dataOut[22] = (cleanStr(trim($organizer_term_id),false) ? cleanStr(trim($organizer_term_id),false) : $na); // evo_organizer_id
       $dataOut[23] = (cleanStr(trim($event_organizer),false) ? cleanStr(trim($event_organizer),false) : $na);     // event_organizer
       $dataOut[24] = (cleanStr(trim($orgdescription),false) ? cleanStr($orgdescription,false) : $na);             // organizer_descriptio
       $dataOut[25] = (cleanStr(trim($org_contact),false) ? cleanStr(trim($org_contact),false) : $na);             // evcal_org_contact
       $dataOut[26] = (cleanStr(trim($evcal_org_address),false) ? cleanStr(trim($evcal_org_address),false) : $na);          //evcal_org_address
       $dataOut[27] = (cleanStr(trim($evcal_org_exlink),false) ? cleanStr(trim($evcal_org_exlink),false) : $na);
       $dataOut[28] = (cleanStr(trim($evo_org_img),false) ? cleanStr(trim($evo_org_img),false) : $na);
    }else{
          $dataOut[22] = $na;
          $dataOut[23] = $na;
          $dataOut[24] = $na;
          $dataOut[25] = $na;
          $dataOut[26] = $na;
          $dataOut[27] = $na;
          $dataOut[28] = $na;
    }
  //  echo "<td>$dataOut[22]</td><td>$dataOut[23]</td><td>$dataOut[24]</td><td>$dataOut[25]</td><td>$dataOut[26]</td><td>$dataOut[27]</td><td>$dataOut[28]</td>";



          // ------------      evcal_subtitle  -------------//
          $evcal_subtitle =  (!empty($pmv['evcal_subtitle']) ? $pmv['evcal_subtitle'][0]: $na);
           $dataOut[29] = cleanStr(trim($evcal_subtitle),false);
   //     echo "<td>$dataOut[29]</td>";

      // ------------      learn more link  -------------//
          $learnmore_link =  (!empty($pmv['evcal_lmlink']) ? $pmv['evcal_lmlink'][0]: $na);
           $dataOut[30] = cleanStr(trim($learnmore_link),false);
   //     echo "<td>$dataOut[30]</td>";


        // ------------      image_url  ------------//
    
			$img_id =get_post_thumbnail_id($__id);
			if($img_id!=''){
				$img_src = wp_get_attachment_image_src($img_id,'full');
				$this_img = $img_src[0];
			}else{ $this_img = $na;}


			if($this_img =='' && !empty($pmv['image_url'])){
				$value = html_process_content($pmv['image_url'][0], $process_html_content);
				$this_img = $value;
			}			

	

        if (strlen(trim($this_img)) > 0){
           $dataOut[31] = cleanStr(trim($this_img),false);
        }else{
           $dataOut[31] = $na;
        }       
//       echo "<td>$dataOut[31]</td>";

        //----      repeatevent  ------------//
       // var evcal_repeat val repeatevent
       $evcal_repeat= ( (!empty($pmv['evcal_repeat'])) ? $pmv['evcal_repeat'][0]: 'no');
       $dataOut[32] = cleanStr(trim($evcal_repeat),true);
 //      echo "<td>$dataOut[32]</td>";

        // ------------      frequency  ------------//
        // var  evcal_rep_freq  val frequency

        $evcal_rep_freq= ( (!empty($pmv['evcal_rep_freq'][0])) ? $pmv['evcal_rep_freq'][0]: $na);
        $dataOut[33] = cleanStr(trim($evcal_rep_freq),true);
 //       echo "<td>$dataOut[33]</td>";

        // ------------       repeats  ------------//
        // var evcal_repeat val repeatevent
        $evcal_rep_num = ( (!empty($pmv['evcal_rep_num'][0])) ? $pmv['evcal_rep_num'][0]:$na);
        $dataOut[34] = cleanStr(trim( $evcal_rep_num),true);
 //       echo "<td>$dataOut[34]</td>";

       // ------------      repeatby  ------------//


       // var evp_repeat_rb val epeatby
       $evp_repeat_rb= ( (!empty($pmv['evp_repeat_rb'])) ? $pmv['evp_repeat_rb'][0]: $na);
       $dataOut[35] = cleanStr(trim($evp_repeat_rb),true);
 //       echo "<td>$dataOut[35]</td>";

       // ------------       event_type      ------------//
        $startPos = 36;
       foreach($evt as $k_name => $e_slug){
         $terms = get_the_terms( $__id, $k_name );

       
if ( $terms && ! is_wp_error( $terms ) ){
        $term_id = '';
	foreach ( $terms as $term ) {
	   $term_id .= $term->term_id.',';
	}

         $dataOut[$startPos] = cleanStr(trim( $term_id),true);

         // name
//         echo '<td>'.$dataOut[$startPos].'</td>';

         $startPos ++;

        // slug
        $slug ='';
	foreach ( $terms as $term ) {
		$slug .= $term->slug.',';
	}
$dataOut[$startPos] = cleanStr(trim($slug),true);
//        echo '<td>'.$dataOut[$startPos].'</td>';
         $startPos ++;
  
}else{
  $dataOut[$startPos] = $na;
 // echo '<td>'.$dataOut[$startPos].'</td>';
  $startPos ++;
  $dataOut[$startPos] = $na;
//  echo '<td>'.$dataOut[$startPos].'</td>';
  $startPos ++;
}

}


  $startPos = 45;
	for($z=1; $z<=$cmd_count;  $z++){
							$cmd_name = '_evcal_ec_f'.$z.'a1_cus';
							$cmd_name_ = 'cmd_'.$z;
							if(!empty($pmv[$cmd_name])){
                                                           $cut =  str_replace('"', "'", html_process_content($pmv[$cmd_name][0], $process_html_content));

                                                        }else{
						           $cut =  (!empty($pmv[$cmd_name_])?str_replace('"', "'", html_process_content($pmv[$cmd_name_][0], $process_html_content)):$na);
							}
							$dataOut[$startPos] = cleanStr(trim($cut),false);
                                                        //  echo '<td>'.$dataOut[$startPos].'</td>';
							$startPos ++;
						}

// echo "<td>$dataOutStatus";

  

if($dataOutStatus == true){

 fputcsv($handle, $dataOut,',');
// echo " - saving";
}
//echo "</td>";
 //     echo '</tr>';

       $dataOut = array();
       $row ++;  
     } // end while
 // echo '</table>';

 fclose($handle);


