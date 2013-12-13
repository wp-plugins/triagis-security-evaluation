<?php
if( !defined( 'ABSPATH' ))
	require '../plugin.php';

# W4 Databae Query FrameWork
# Modifications are not recommended.
function w4sl_query( $args = array(), $results = true ){
	$w4sl_query = new W4LS_Query( $args );
	
	if( $results === false )
		return $w4sl_query;

	$w4sl_query->query();
	return $w4sl_query->results();
}

class W4LS_Query{
	var $query_args;
	var $request;
	var $primary_table;
	var $joined_table = array();
	var $errors;

	var $found_item = 0;
	var $limit = '';
	var $page = 1;
	var $max_pages = 1;
	var $qr = 'get_results';

	function __construct( $query_args ){
		$this->query_args = $query_args;
	}
	function set( $key, $val ){
		$this->query_args[$key] = $val;
	}
	function get( $key ){
		return isset( $this->query_args[$key] ) ? $this->query_args[$key] : '';
	}
	function set_table(){
		global $wpdb;

		if( '' == $this->get( 'table' )){
			$this->errors[] = "Table Not Defined";
			return;
		}

		$allowed = array( 'w4sl_log', 'w4sl_spamlist', 'w4sl_visitors' );

		if( !in_array( $this->get( 'table' ), $allowed ))
			$this->errors[] = "Quering for table is not allowed.:" . $this->get( 'table' );

		foreach( $allowed as $table ){
			$this->$table = $wpdb->prefix. $table;
		}

		$this->primary_table = $wpdb->prefix . $this->get( 'table' );
	}

	function parse_query_vars(){
		if( !is_array( $this->query_args ))
			$this->query_args = array();

		$this->set_table();

		if( '' == $this->get( 'order' ) || !in_array( strtoupper( $this->get( 'order' )), array( 'ASC', 'DESC' )))
			$this->set( 'order', "ASC" );

		$this->set( 'order', strtoupper( $this->get( 'order' )));

		if ( '' == $this->get( 'page' )){
			$this->set( 'page', $this->page );
		}
		else{
			$this->page = $this->get( 'page' ) < 1 ? 1 : $this->get( 'page' );
		}

		if ( '' != $this->get( 'limit' )){
			$this->limit = absint( $this->get( 'limit' ));
		}

		$this->output = $this->get( 'output' ) ? $this->get( 'output' ) : OBJECT;
	}

	function init(){
		$this->primary_table = "";
		$this->output =  "";
		$this->_select = "";
		$this->_fields = "";
		$this->_found_rows = "";
		$this->_join = "";
		$this->_where = "";
		$this->_groupby = "";
		$this->_order = "";
		$this->_limit = "";
		$this->_qr = "";
	}

	function query(){
		$this->init();
		$this->parse_query_vars();

		if( !empty( $this->errors ))
			return;

		global $wpdb;
		
		$this->_select = "SELECT";
		$this->_join = " FROM $this->primary_table AS TB";
		$this->_where = " WHERE 1=1";

		if( '' != $this->get( 'column' )){
			$this->_fields .= " TB.". $this->get( 'column' ) ."";
		}
		elseif( '' != $this->get( 'columns' )){
			$this->_fields .= " TB.". implode( ", TB.", $this->get( 'columns' )) ."";
		}
		elseif( $this->get( 'qr' ) == 'count_row' ){
			$this->_fields .= " COUNT(*)";
		}
		else{
			$this->_fields .= " TB.*";
		}

		if( $this->primary_table == $this->w4sl_log ){
			if( '' != $this->get( 'log_id' )){
				$this->_where .= " AND log_id = '". $this->get( 'log_id' ) ."'";
				$this->_qr = 'get_row';
			}
			if( '' != $this->get( 'user_id' )){
				$this->_where .= " AND user_id = '". $this->get( 'user_id' ) ."'";
			}
			if( '' != $this->get( 'session_id' )){
				$this->_where .= " AND session_id = '". $this->get( 'session_id' ) ."'";
			}
			if( '' != $this->get( 'ip_address' )){
				$this->_where .= " AND ip_address = '". $this->get( 'ip_address' ) ."'";
			}
			if( '' != $this->get( 'session_id' )){
				$this->_where .= " AND session_id = '". $this->get( 'session_id' ) ."'";
			}

			if( $this->get( 'join_name' ) == true ){
				$this->_fields .= ", WPU.display_name AS name";
				$this->_join .= " LEFT JOIN $wpdb->users WPU ON (WPU.ID = TB.user_id)";
			}
		}
		elseif( $this->primary_table == $this->w4sl_visitors ){
			if( '' != $this->get( 'visitor_id' )){
				$this->_where .= " AND visitor_id = '". $this->get( 'visitor_id' ) ."'";
				$this->_qr = 'get_row';
			}
			if( '' != $this->get( 'visitor_ip' )){
				$this->_where .= " AND visitor_ip = '". $this->get( 'visitor_ip' ) ."'";
			}
			if( '' != $this->get( 'visitor_url' )){
				$this->_where .= " AND visitor_url = '". $this->get( 'visitor_url' ) ."'";
			}
			if( '' != $this->get( 'visitor_agent' )){
				$this->_where .= " AND visitor_agent = '". $this->get( 'visitor_agent' ) ."'";
			}

			if( '' != $this->get( 'start_date' ) || '' != $this->get( 'end_date' )){
				$start_date = $this->get( 'start_date' );
				if( !isset( $start_date ) || !preg_match('#([0-9]{2})-([0-9]{1,2})-([0-9]{1,2})#', $start_date ))
					$start_date = date( 'y-m-d', time() + ( get_option( 'gmt_offset' ) * 60 ));

				$start_time = $this->get( 'start_time' );
				if( !isset( $start_time ) || !preg_match('#([0-9]{2}):([0-9]{2})#', $start_time ))
					$start_time = '00:00';
				$start_time .= ':00';

				$_start = "{$start_date} {$start_time}";
				$this->_where .= " AND visitor_time >= '$_start'";


				$end_time = $this->get( 'end_time' );
				if( !isset( $end_time ) || !preg_match('#([0-9]{2}):([0-9]{2})#', $end_time ))
					$end_time = '23:55';
				$end_time .= ':00';

				$end_date = $this->get( 'end_date' );
				if( !isset( $end_date ) || !preg_match('#([0-9]{2})-([0-9]{1,2})-([0-9]{1,2})#', $end_date ))
					$end_date = $start_date;

				$_end = "{$end_date} {$end_time}";

				$this->_where .= " AND visitor_time <= '$_end'";
			}
			
			if( '' != $this->get( 'gb' )){
				$this->_fields .= ", COUNT(*) AS group_count";
				$this->_groupby .= " GROUP BY ". $this->get( 'gb' );
			}
		}
		elseif( $this->primary_table == $this->w4sl_spamlist ){
			if( '' != $this->get( 'list_id' )){
				$this->_where .= " AND list_id = '". $this->get( 'list_id' ) ."'";
				$this->_qr = 'get_row';
			}
		}
		if( '' != $this->get( 's' )){
			$s = stripslashes( $this->get( 's' ));
			$sb = $this->get( 'sb' );

			preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $s, $matches );
			$search_terms = array_map( '_search_terms_tidy', $matches[0] );

			$n = '%';
			$searchand = '';
			$search = '';

			foreach( (array) $search_terms as $term ){
				$term = esc_sql( like_escape( $term ));
				$search .= "{$searchand}($sb LIKE '{$n}{$term}{$n}')";
				$searchand = ' OR ';
			}

			if( !empty( $search )){
				$this->_where .= " AND ({$search}) ";
			}
		}

		if( '' != $this->get( 'orderby' )){
			$order = $this->get( 'order' );
			$orderby = $this->get( 'orderby' );
			$this->_order .= " ORDER BY $orderby $order";
		}

		if( '' != $this->limit ){
			if ( '' == $this->get( 'offset' )){
				$start = ( $this->page - 1 ) * $this->limit . ', ';
				$this->_limit .= ' LIMIT ' . $start . $this->limit;
			}
			else{
				$this->set( 'offset', absint( $this->get( 'offset' )));
				$start = $this->get( 'offset' ) . ', ';
				$this->_limit .= ' LIMIT ' . $start . $this->limit;
			}
		}
		if( '' != $this->limit ){
			$this->_found_rows = " SQL_CALC_FOUND_ROWS";
		}

		$this->request = $this->_select . $this->_found_rows . $this->_fields . $this->_join . $this->_where . $this->_groupby . $this->_order . $this->_limit;
		$this->request = apply_filters( 'w4sl_query_request', $this->request );
		
		#echo $this->request;
	}

	function results(){
		global $wpdb;

		if( !empty( $this->errors )){
			$error_obj = new WP_Error();
			foreach( $this->errors as $error )
				$error_obj->add( 'error', $error );
			return $error_obj;
		}

		if( !empty( $this->errors ))
			return new WP_Error( 'error', $this->errors );

		if( '' == $this->get( 'qr' )){
			if( '' != $this->_qr )
				$this->set( 'qr', $this->_qr );
			elseif( '' != $this->get( 'column' ))
				$this->set( 'qr', 'get_col' );
		}

		#$this->set( 'qr', 'get_var' );
		#echo $this->get( 'column' );
		#echo $this->get( 'qr' );

		if( !in_array( $this->get( 'qr' ), array( 'get_row', 'get_var', 'get_col', 'count_row' )))
			$this->set( 'qr', 'get_results' );

		if( $this->get( 'qr' ) == 'get_col' ){
			$result = $wpdb->get_col( $this->request );
		}
		elseif( $this->get( 'qr' ) == 'count_row' || $this->get( 'qr' ) == 'get_var' ){
			$result = $wpdb->get_var( $this->request );
		}
		elseif( $this->get( 'qr' ) == 'get_row' ){
			$result = $wpdb->get_row( $this->request, $this->output );
		}
		else{
			$result = $wpdb->get_results( $this->request, $this->output );
		}

		if( '' != $this->limit ){
			$this->found_item = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
			$this->max_pages = ceil( $this->found_item / $this->limit );
		}
		else{
			$this->found_item = count( $result );
			$this->max_pages = 1;
		}

		return $result;
	}
}
?>