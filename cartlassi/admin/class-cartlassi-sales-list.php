<?php 

class Sales_List extends WP_List_Table {

    /** Class constructor */
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Sale', 'cartlassi' ), //singular name of the listed records
            'plural' => __( 'Sales', 'cartlassi' ), //plural name of the listed records
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
    public static function get_sales( $per_page = 10, $page_number = 1 ) {
        $apiKey = self::getApiKey();

		$args = array(
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
		);
		
		$response = wp_remote_get( "http://host.docker.internal:3000/shops/sales-as-seller", $args );

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
		
		$response = wp_remote_get( "http://host.docker.internal:3000/shops/sales-as-seller", $args );

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
        _e( 'No sales avaliable.', 'cartlassi' );
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
        // var_dump('column_default');
        // var_dump($item);
        // var_dump($column_name);
        return $item[ $column_name ];
    }

    /**
    * Associative array of columns
    *
    * @return array
    */
    function get_columns() {
        // var_dump('get_columns');

        $columns = array(
            // 'cb'            => '<input type="checkbox" />',
            'id'         => __( 'ID', 'cartlassi' ),
            // 'shopId'         => __( 'Shop ID', 'cartlassi' ),
            // 'clickId'         => __( 'Click ID', 'cartlassi' ),
            'amount'        => __( 'Sale Amount', 'cartlassi' ),
            'currency'       => __( 'Currency', 'cartlassi' ),
            'status'       => __( 'Status', 'cartlassi' ),
            'createdAt'        => __( 'created at', 'cartlassi' ),
            'commissionDueDate' => __( 'Commission at', 'cartlassi' ),
            // 'updatedAt'    => __( 'updated at', 'cartlassi' ),
        );
        
        return $columns;
    }

    /**
    * Columns to make sortable.
    *
    * @return array
    */
    // public function get_sortable_columns() {
    //     // var_dump('get_sortable_columns');
    //     $sortable_columns = array(
    //         'id'         => array( 'id', false ),
    //         'shopId'         => array( 'shopId', false ),
    //         'clickId'         => __( 'clickId', false ),
    //         'amount'        => __( 'amount', false ),
    //         'currency'       => __( 'currency', false ),
    //         'status'       => __( 'status', false ),
    //         'commissionDueDate' => __( 'commissionDueDate', false ),
    //         'createdAt'        => __( 'createdAt', true ),
    //         'updatedAt'    => __( 'updatedAt',false ),
    //     );
        
    //     return $sortable_columns;
    // }
    
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
        
        $per_page = $this->get_items_per_page( 'sales_per_page', 10 );
        // var_dump($per_page);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        // var_dump($current_page);
        // var_dump($total_items);
        
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ] );
        
        $this->items = self::get_sales( $per_page, $current_page );
        // echo var_export($this->items, true).'<br/>';
    }


    public function process_bulk_action() {

        // //Detect when a bulk action is being triggered...
        // if ( 'delete' === $this->current_action() ) {
        
        // // In our file that handles the request, verify the nonce.
        // $nonce = esc_attr( $_REQUEST['_wpnonce'] );
        
        // if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
        // die( 'Go get a life script kiddies' );
        // }
        // else {
        // self::delete_customer( absint( $_GET['customer'] ) );
        
        // wp_redirect( esc_url( add_query_arg() ) );
        // exit;
        // }
        
        // }
        
        // // If the delete bulk action is triggered
        // if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
        // || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        // ) {
        
        // $delete_ids = esc_sql( $_POST['bulk-delete'] );
        
        // // loop over the array of record IDs and delete them
        // foreach ( $delete_ids as $id ) {
        // self::delete_customer( $id );
        
        // }
        
        // wp_redirect( esc_url( add_query_arg() ) );
        // exit;
        // }
    }

    protected static function getApiKey() {
		return get_option(Cartlassi_Constants::OPTIONS_NAME)[Cartlassi_Constants::API_KEY_FIELD_NAME];
	}
    
}
