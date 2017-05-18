<?php header('Content-Type: text/css'); ?>
[class^=col-] { float:left;width:99.9% }
@media all and (min-width: 720px) {
<?php
    function string_format( $template, $data, $prefix = "" ) {
        foreach ($data as $key => $value) {
            $compositeKey = $prefix . $key;
            switch(gettype($value)) {
                case 'string':
                case 'double':
                case 'float':
                case 'integer':
                    $template = str_replace( '{'.$compositeKey.'}', $value, $template);
                    break;
                case 'boolean':
                    $template = str_replace( '{'.$compositeKey.'}', $value ? 'true' : 'false', $template);
                    break;
                case 'object':
                    $value = (array) $value;
                case 'array':
                    $template = string_format( $template, $value, $compositeKey . '.');
                    break;
            }
        }
        return $template;
    }
    function print_grid( $columns, $current = 1 ) {
      if ( $current > $columns ) {return;}
      print(string_format("  .col-{current}-{columns} {width:{width}%}\n",array(
          'columns'=>$columns,
          'current'=>$current,
          'width'=>$current/$columns*99.9
      )));
      print_grid($columns,$current+1);
    }
    $grid = array( 3, 5, 9, 12 );
    foreach ($grid as $columns) {
        print_grid($columns);
    }
?>
}
