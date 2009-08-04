var runningAhref = false;
var runningSpanlog = false;
var runningUnit = false;

/**
 * Executve one unit test, called from index.html
 *
 * @param $ Element, the caller 
 * @param string Unique name of the unit test
 * @return void
 */
function run(ahref, spanlog, unit) {
  
    // if some test is running now
    if (runningUnit !== false) {
        return;
    }

    runningAhref = ahref;
    runningSpanlog = spanlog;
    runningUnit = unit;

    // clear log field
    spanlog.hide();

    // set waiting status to mouse-cursor on this span
    ahref.css('cursor', 'wait');

    // set temporary message
    runningSpanlog.html('started...').show();
    $('#report').html('waiting...');
    $('#protocol').empty();
    $('#output').empty().css('cursor', 'wait');

    _runRoutine();
    
}

/**
 * Stop current testing
 *
 * @param string Unique name of the unit test
 * @return void
 */
function stop(unit) {

    // if some test is running now
    if (runningUnit === false) {
        return;
    }

    runningUnit = false;

    // stop running
    $.ajax({
        url: "<?=$this->url(array('action'=>'stop'), 'units', true)?>",
        type: "POST",
        data: {name: runningUnit},
        dataType: "json",

        success: function(json) {
        }
    });
            
}  

/**
 * Executve one unit test
 *
 * @return void
 */
function _runRoutine() {

    // sanity check
    if (runningUnit === false) {
        return;
    }

    // get unit test results
    $.ajax({
        url: "<?=$this->url(array('action'=>'run'), 'units', true)?>",
        type: "POST",
        data: {name: runningUnit},
        dataType: "json",

        success: function(json) {
            
            $('#output').html(json['output']);
            $('#protocol').html(json['protocol']);
            runningSpanlog.html(json['spanlog']);

            // if the testing is finished
            if ((json['finished'] === true) || (runningUnit === false)) {
    
                $('#output').css('cursor', 'default');
    
                // set cursor back to normal
                runningAhref.css('cursor', 'pointer');

                // allow new tests to start
                runningUnit = false;

            } else {

                // call again in 0.5 seconds
                setTimeout("_runRoutine()", 500);

            }

        }
               
    });

}	


