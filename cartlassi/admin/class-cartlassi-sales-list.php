<?php 

class Sales_List extends WP_List_Table {

    protected $config;
    protected $api; 
    /** Class constructor */
    public function __construct($config, $api) {

        parent::__construct( [
            'singular' => __( 'Sale', Cartlassi_Constants::TEXT_DOMAIN ), //singular name of the listed records
            'plural' => __( 'Sales', Cartlassi_Constants::TEXT_DOMAIN ), //plural name of the listed records
            'ajax' => false //should this table support ajax?
        ] );

        $this->config = $config;
        $this->api = $api;
    }

    /**
    * Retrieve customer’s data from the database
    *
    * @param int $per_page
    * @param int $page_number
    *
    * @return mixed
    */    
    public static function get_sales( $api, $per_page = 10, $page_number = 1 ) {
        return $api->request('/shops/sales-as-seller', [], true);
    }

    /**
    * Returns the count of records in the database.
    *
    * @return null|string
    */
    public static function record_count($api) {
        $data = $api->request('/shops/sales-as-seller', [], true);
        return count($data);
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No sales avaliable.', Cartlassi_Constants::TEXT_DOMAIN );
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

    public function column_shopOrderId ( $item ) {

        $order = wc_get_order( $item['shopOrderId'] );
        $customer_id = $order->get_customer_id();
        $customer = new WC_Customer( $customer_id );
 
        return '<a href="/wp-admin/post.php?post='.$item['shopOrderId'].'&action=edit">#'.$item['shopOrderId'].' '.$customer->get_first_name().' '.$customer->get_last_name().'</a>';
    }

    public function column_shopProductId ( $item ) {

        $product = wc_get_product( $item['shopProductId'] );
 
        return $product->get_image();
    }

    public function column_commissionAmount ( $item ) {
        return wc_price($item['amount'] * Cartlassi_Constants::COMMISSION, array (
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
            'shopOrderId'       => __( 'Order',Cartlassi_Constants::TEXT_DOMAIN ),
            'shopProductId'     => __( 'Product', Cartlassi_Constants::TEXT_DOMAIN ),
            'amount'            => __( 'Sale Amount', Cartlassi_Constants::TEXT_DOMAIN ),
            'commissionAmount' => __( 'Commission Amount', Cartlassi_Constants::TEXT_DOMAIN ),
            'createdAt'         => __( 'created at', Cartlassi_Constants::TEXT_DOMAIN ),
            'commissionDueDate' => __( 'Commission at',Cartlassi_Constants::TEXT_DOMAIN ),
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
        
        $per_page = $this->get_items_per_page( 'sales_per_page', 10 );
        // var_dump($per_page);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count($this->api);

        // var_dump($current_page);
        // var_dump($total_items);
        
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ] );
        
        $this->items = self::get_sales( $this->api, $per_page, $current_page );
        // echo var_export($this->items, true).'<br/>';
    }

    // protected static function getApiKey() {
	// 	return get_option(Cartlassi_Constants::API_OPTIONS_NAME)[Cartlassi_Constants::API_KEY_FIELD_NAME];
	// }
    
}
