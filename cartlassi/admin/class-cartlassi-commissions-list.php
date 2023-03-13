<?php 

class Commissions_List extends WP_List_Table {

    /** Class constructor */
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Commission', Cartlassi_Constants::TEXT_DOMAIN ), //singular name of the listed records
            'plural' => __( 'Commissions', Cartlassi_Constants::TEXT_DOMAIN ), //plural name of the listed records
            'ajax' => false //should this table support ajax?
        ] );
    }

    /**
    * Retrieve customer’s data from the database
    *
    * @param int $per_page
    * @param int $page_number
    *
    * @return mixed
    */    
    public static function get_commissions( $per_page = 10, $page_number = 1 ) {
        $apiKey = self::getApiKey();

		$args = array(
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
		);
		
		$response = wp_remote_get( "http://host.docker.internal:3000/shops/sales-as-promoter", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log("WWWWWWWWWWW ${error_message}");
			return wp_send_json_error($response);
		}
        $body = wp_remote_retrieve_body( $response );
        // echo $body;
        $data = json_decode( $body, true );
        // var_dump($data);
        // TBD handle pagination here, i.e. extract only the portion required from $data
        return $data;
				
    }

    /**
    * Returns the count of records in the database.
    *
    * @return null|string
    */
    public static function record_count() {
        // var_dump('in counttttttt');
        $apiKey = self::getApiKey();

		$args = array(
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
		);
		
		$response = wp_remote_get( "http://host.docker.internal:3000/shops/sales-as-promoter", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log("WWWWWWWWWWW ${error_message}");
			return wp_send_json_error($response);
		}
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body );
        return count($data);
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No commissions avaliable.', Cartlassi_Constants::TEXT_DOMAIN );
    }


    /**
    * Render a column when no column specific method exists.
    *
    * @param array $item
    * @param string $column_name
    *
    * @return mixed
    */
    public function column_default( $item, $column_name ) {
        return $item[ $column_name ];
    }

    public function column_createdAt ( $item ) {
        return date_format(date_create($item['createdAt']), "Y/m/d H:i:s");
    }

    public function column_commissionDueDate ( $item ) {
        return date_format(date_create($item['commissionDueDate']), "Y/m/d H:i:s");
    }

    public function column_amount ( $item ) {
        return wc_price($item['amount'], array (
            'currency'  => $item['currency']
        ) );
    }

    // public function column_shopOrderId ( $item ) {

    //     $order = wc_get_order( $item['shopOrderId'] );
    //     $customer_id = $order->get_customer_id();
    //     $customer = new WC_Customer( $customer_id );
 
    //     return '<a href="/wp-admin/post.php?post='.$item['shopOrderId'].'&action=edit">#'.$item['shopOrderId'].' '.$customer->get_first_name().' '.$customer->get_last_name().'</a>';
    // }

    // public function column_shopProductId ( $item ) {

    //     $product = wc_get_product( $item['shopProductId'] );
 
    //     return $product->get_image();
    // }

    public function column_commissionAmount ( $item ) {
        return wc_price($item['amount'] * Cartlassi_Constants::COMMISSION - $item['amount'] * Cartlassi_Constants::FEE, array (
            'currency'  => $item['currency']
        ) );
    }

    /**
    * Associative array of columns
    *
    * @return array
    */
    function get_columns() {
        // var_dump('get_columns');

        $columns = array(
            'id'                => __( 'ID', Cartlassi_Constants::TEXT_DOMAIN ),
            'amount'            => __( 'Sale Amount', Cartlassi_Constants::TEXT_DOMAIN ),
            'commissionAmount' => __( 'Commission Amount', Cartlassi_Constants::TEXT_DOMAIN ),
            'createdAt'         => __( 'created at', Cartlassi_Constants::TEXT_DOMAIN ),
            'commissionDueDate' => __( 'Commission at', Cartlassi_Constants::TEXT_DOMAIN ),
            'status'            => __( 'Status', Cartlassi_Constants::TEXT_DOMAIN ),
        );
        
        return $columns;
    }

    /**
    * Columns to make sortable.
    *
    * @return array
    */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'commissionDueDate' => __( 'commissionDueDate', false ),
            'createdAt'        => __( 'createdAt', true ),
        );
        
        return $sortable_columns;
    }
    
    /**
    * Returns an associative array containing the bulk action
    *
    * @return array
    */
    public function get_bulk_actions() {
        $actions = [
        ];
        
        return $actions;
    }    


    /**
    * Handles data query and filter, sorting, and pagination.
    */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();
        // echo var_export($this->_column_headers, true).'<br/>';
        /** Process bulk action */
        // $this->process_bulk_action();
        
        $per_page = $this->get_items_per_page( 'commissions_per_page', 10 );
        // var_dump($per_page);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        // var_dump($current_page);
        // var_dump($total_items);
        
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ] );
        
        $this->items = self::get_commissions( $per_page, $current_page );
        // echo var_export($this->items, true).'<br/>';
    }

    protected static function getApiKey() {
		return get_option(Cartlassi_Constants::OPTIONS_NAME)[Cartlassi_Constants::API_KEY_FIELD_NAME];
	}
    
}
