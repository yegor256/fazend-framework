/**
 * Executve one unit test
 *
 * @param $ Element, the caller 
 * @param string Unique name of the unit test
 * @return void
 */
function run(ahref, spanlog, unit) {

    // clear log field
    spanlog.hide();

    // set waiting status to mouse-cursor on this span
    ahref.css('cursor', 'wait');

    // set temporary message
    $('#report').html('waiting...');
    $('div#protocol').empty();
    $('pre#output').empty().css('cursor', 'wait');

    // get unit test results
    $.ajax({
        url: "<?=$this->url(array('action'=>'run'), 'units', true)?>",
        type: "POST",
        data: {name: unit},
        dataType: "json",

        success: function(json) {
            
            $('pre#output').html(json['output']).css('cursor', 'text');
            $('div#protocol').html(json['protocol']);

            spanlog.html(json['spanlog']).show();

            // set cursor back to normal
            ahref.css('cursor', 'pointer');

        }
               
    });

}	

