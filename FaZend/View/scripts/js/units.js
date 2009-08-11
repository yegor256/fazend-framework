
var runningAhref = false;
var runningSpanlog = false;
var runningUnit = false;

/**
 * Update information on-screen from JSON
 *
 * @return void
 */
var _refreshScreen = function(json) {
            
    // sanity check
    if (json === null) {
        return;
    }
    
    $('#output').html(json['output']);
    $('#protocol').html(json['protocol']);
    
    if (json['spanlog']) {
        runningSpanlog.html(json['spanlog']);
    }

    // if the testing is finished
    if ((json['finished'] === true) || (runningUnit === false)) {

        // make CSS changes
        _cssFinished();

        // allow new tests to start
        runningUnit = false;

    } else {

        // call again in 0.5 seconds
        setTimeout("_runRoutine()", 500);

    }

}

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

    // make CSS changes
    _cssStarted();

    // stop running
    $.ajax({
        url: "<?=$this->url(array('action'=>'run'), 'units', true)?>",
        type: "POST",
        data: {name: runningUnit},
        dataType: "json",
        success: _refreshScreen,
    });
    
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

    // make CSS changes
    _cssFinished();

    unit = runningUnit;
    runningUnit = false;

    // stop running
    $.ajax({
        url: "<?=$this->url(array('action'=>'stop'), 'units', true)?>",
        type: "POST",
        data: {name: unit},
        dataType: "json"
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
        url: "<?=$this->url(array('action'=>'routine'), 'units', true)?>",
        type: "POST",
        data: {name: runningUnit},
        dataType: "json",
        success: _refreshScreen,
    });

}	

/**
 * CSS changes when test is just started
 *
 * @return void
 */
function _cssStarted() {

    // set waiting status to mouse-cursor on this span
    runningAhref.css('cursor', 'wait');

    // set temporary message
    runningSpanlog.html('started...').show();

    $('#report').html('waiting...');
    $('#protocol').empty();
    $('#output').empty().css('cursor', 'wait');

}

/**
 * CSS changes when test is finished
 *
 * @return void
 */
function _cssFinished() {

    $('#output').css('cursor', 'default');

    // set cursor back to normal
    runningAhref.css('cursor', 'pointer');

}


