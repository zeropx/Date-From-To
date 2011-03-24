<?php 

/**
* Date From-To display class
*/
class Date_from_to
{
  
  function __construct()
  {
    # code...
  }

  
  /**
   * date_range
   *
   * Can return displayed string of the date, or array
   * 
   *
   * @param string $start_date 
   * @param string $end_date 
   * @param string $return_array 
   * @return string || array
   * @author Eric Casequin
   * @notes I want to adjust and change the way this works later since it isn't working as wanted.
   * rough code is placed for quick production.
   */
  function _date_range( $start_date = "", $end_date = "", $return_array = false) 
  {

    $args = func_get_args();

    /*
      Just in case want to send an array of settings.
    */
    if(is_array($start_date))
    {
      $start_date     = $args[0]['start'];
      $end_date       = $args[0]['end'];
      $display_format = $args[0]['format'];
      $return_array   = $args[0]['array'];
    }
    unset($args);


    if(empty($end_date) || strtotime($start_date) >= strtotime($end_date)) $end_date = $start_date;

    $obj = new stdClass();

    /// Original timestamps
    $obj->timestamp = new stdClass();
    $obj->timestamp->current = strtotime(date('Y-m-d H:i:s'));
    $obj->timestamp->start = strtotime($start_date);
    $obj->timestamp->end = strtotime($end_date);

    /// Array Time for use in differences
    $obj->ts->current = explode(" ", date('Y m d H i s A'));
    $obj->ts->start   = explode(" ", date('Y m d H i s A', $obj->timestamp->start));
    $obj->ts->end     = explode(" ", date('Y m d H i s A', $obj->timestamp->end));

    /// Current Date
    $obj->ts_current = new stdClass();
    $obj->ts_current->year  = $obj->ts->current[0];
    $obj->ts_current->month = $obj->ts->current[1];
    $obj->ts_current->day   = $obj->ts->current[2];
    $obj->ts_current->time  = $obj->ts->current[3] . ":".$obj->ts->start[4];
    $obj->ts_current->ampm  = $obj->ts->current[6];

    /// Start date
    $obj->ts_start = new stdClass();
    $obj->ts_start->year  =  $obj->ts->start[0];
    $obj->ts_start->month =  $obj->ts->start[1];
    $obj->ts_start->day   =  $obj->ts->start[2];
    $obj->ts_start->time  =  $obj->ts->start[3] . ":".$obj->ts->start[4];
    $obj->ts_start->ampm  =  $obj->ts->start[6];

    /// End date
    $obj->ts_end = new stdClass();
    $obj->ts_end->year  =  $obj->ts->end[0];
    $obj->ts_end->month =  $obj->ts->end[1];
    $obj->ts_end->day   =  $obj->ts->end[2];
    $obj->ts_end->time  =  $obj->ts->end[3] . ":".$obj->ts->start[4];
    $obj->ts_end->ampm  =  $obj->ts->end[6];

    /// Differences
    $obj->dif = new stdClass();
    $obj->dif->year  = ($obj->ts_end->year  > $obj->ts_start->year) || ($obj->ts_start->year > $obj->ts_current->year)  ? true : false;
    $obj->dif->month = $obj->ts_end->month > $obj->ts_start->month ? true : false;
    $obj->dif->day   = $obj->ts_end->day   > $obj->ts_start->day   ? true : false;
    $obj->dif->time  = $obj->ts_end->time  > $obj->ts_start->time  ? true : false;
    $obj->dif->ampm  = $obj->ts_end->ampm  > $obj->ts_start->ampm  ? true : false;

    /// for Display
    $obj->display = new stdClass();
    $obj->display->start = new stdClass();
    $obj->display->start->year  = date("Y", $obj->timestamp->start);
    $obj->display->start->month = date("F", $obj->timestamp->start);
    $obj->display->start->day   = date("j", $obj->timestamp->start);
    $obj->display->start->time  = date("g:i", $obj->timestamp->start);
    $obj->display->start->ampm  = $obj->ts_start->ampm;

    $obj->display->end = new stdClass();
    $obj->display->end->year  = date("Y", $obj->timestamp->end);
    $obj->display->end->month = date("F", $obj->timestamp->end);
    $obj->display->end->day   = date("j", $obj->timestamp->end);
    $obj->display->end->time  = date("g:i", $obj->timestamp->end);
    $obj->display->end->ampm  = $obj->ts_end->ampm;

    /// Start Display build
    /// By Default, always show the month/day/time it starts

    $obj->display->view = "{$obj->display->start->month} {$obj->display->start->day}";
    /// $obj->display->view .= ", {$obj->display->start->year}";
    /// $obj->display->view .= " {$obj->display->start->time} {$obj->display->start->ampm}";

    /// Abstract 
    $obj->display->abstract = new stdClass();
    $obj->display->abstract->start = "{$obj->display->start->month} {$obj->display->start->day}";
    if($obj->dif->year) $obj->display->abstract->start .= ", {$obj->display->start->year}";
    $obj->display->abstract->start .= " {$obj->display->start->time} {$obj->display->start->ampm}";


    /// Durational catch
    if(  true === $obj->dif->year
      || true === $obj->dif->month
      || true === $obj->dif->day)
      {

        /*
          TODO: Adjust this code to display more logically rather then so directly. 
          Add flexibility to years.

          - from-month from-day from year to* to-month to-day to-year
          - January 23, 2009 - February 1, 2009
          - format options[]
        */
        $obj->display->view .= " - ";
        $end_stamp = "";

        if($obj->dif->month || $obj->dif->year)
        {
          if($obj->dif->year) $end_stamp .= " {$obj->display->end->month} {$obj->display->end->day}, ";
          else if($obj->dif->month) $end_stamp .= "{$obj->display->end->month} {$obj->display->end->day}, ";


        } else {
           $end_stamp .= "{$obj->display->end->day}, ";
        }
        /// $end_stamp .= " {$obj->display->end->time} {$obj->display->end->ampm}";


        $end_stamp .= " ". $obj->display->end->year;
        $obj->display->abstract->end = $end_stamp;

        $obj->display->view .= $end_stamp;

      } else {
        /// Abstract catch if no duration set
        $obj->display->abstract->end = false;

        //  no differences just add the current year.
        $obj->display->view .= ", ". $obj->display->start->year;
      }

    /// Return array if only the data is needed
    if(true === $return_array) return $obj;

    /// Return the html constructed view. 
    return $obj->display->view;

  }
  
  
}
