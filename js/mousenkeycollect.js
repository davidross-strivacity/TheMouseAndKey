// Need to collect session ID or some other identifying mark to group calculations on

  var DATA = { };

  // Stores mouse tracks until the next focusIn event where they get pushed ta Track object.
  var Mouse = { 'boundries': {'maxX':0, 'minX': 999999, 'maxY':0, 'minY': 999999}, 'data': []};
  DATA['Meta'] = {'startTime': null, 'stopTime':  null};

  DATA['FocusEvents'] = {};


  // New block of mouse tracks are defined with currentFocus_GUID
  var currentFocus_GUID = "NoFocusedInput";
  lastFormID = null;
  lastInputID =  null;

// Template of a to from block of mouse movement
DATA['FocusEvents'][currentFocus_GUID] = {
                                    "focusInTime"    : Date.now(),
                                    "focusOutTime"   : null,
                                    "thisFormId"     : null ,
                                    "thisInputId"    : null,
                                    "lastFormID"     : lastFormID,
                                    "lastInputID"    : lastInputID,
                                //    "activeTime"     : 0, // amount of time user typed, vs just focused
                                //    "totalFocusTime" : 0, // amount of time in focus
                                    "thisInputCordinates"    : null, // location of field on screen
                                    "mouseTracks"    : [], //
                                    "keyStrokes"     : []
                                  };

 function dumpDATA() {
   DATA['Meta']['stopTime'] = Date.now();
  document.getElementById("Data1").value = JSON.stringify(DATA, null, 2);

}


function startTime() {
  DATA['Meta']['startTime'] = Date.now();
}





function uuidv4() {
  return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
  );
}

function getFocusIn(event) {
// Generates mouse track object from last focusOut event
// Probably should post object back at this point vs waiting to submit.

  currentFocus_GUID = uuidv4();
  let formId = event.target.form ? event.target.form.id : null;
  let inputId = event.target ? event.target.id : null;
  let input = { "focusInTime" : event.timeStamp,
                 "focusOutTime": null,
                 "thisFormId": formId,
                 "thisInputId":inputId,
                 "lastFormID" : lastFormID,
                 "lastInputID" :  lastInputID,
                // "activeTime": 0,
                // "totalFocusTime": 0,
                 "thisInputCordinates": event.target.getBoundingClientRect(),
                 "mouseTracks": Mouse,
                 "keyStrokes": [] };
//  drawFocusEvents( Mouse );
//  saySlopes( Mouse );
  DATA['FocusEvents'][currentFocus_GUID] = input;
  // reset Mouse. We only want the track to a focusIn event
  Mouse = { 'boundries': {'maxX':0, 'minX': 999999, 'maxY':0, 'minY': 999999}, 'data': []};

// Set for next getFocusIn event to track field to / from
  lastFormID = formId;
  lastInputID =  inputId;
//  dumpDATA() ;

}

function getFocusOut(event) {
  DATA['FocusEvents'][currentFocus_GUID].focusOutTime = event.timeStamp;
//  DATA['FocusEvents'][currentFocus_GUID].totalFocusTime = DATA['FocusEvents'][currentFocus_GUID].focusOutTime - DATA['FocusEvents'][currentFocus_GUID].focusInTime;
//  console.log(JSON.stringify(DATA['FocusEvents'][currentFocus_GUID], null, 2));
  currentFocus_GUID = "NoFocusedInput";
}

//mouseenter
//mouseleave

function downMouse(event) {
  let input =  { "timestamp" : event.timeStamp,
                         "x" : event.clientX,
                         "y" : event.clientY,
                        "type": "down"
                      };
  Mouse['data'].push( input );
}

function upMouse(event) {
 let input =  { "timestamp" : event.timeStamp,
                        "x" : event.clientX,
                        "y" : event.clientY,
                        "type": "up"
                      };
  Mouse['data'].push( input );
}



function moveMouse(event) {
  let input =  { "timestamp" : event.timeStamp,
                         "x" : event.clientX,
                         "y" : event.clientY,
                          "type": "move" };

  // Cheap hack to remove back to back duplicate coordinates
  let len = Mouse['data'].length;
  if ( len == 0 ){
    Mouse['data'].push( input );
  }
  else if ( Mouse['data'][len-1]['x'] != input['x'] ||
       Mouse['data'][len-1]['y'] != input['y'] ) {
    Mouse['data'].push( input );
  }
  // Establish a box that contains the movements
  // This could be done in the back end maybe faster
  Mouse.boundries.maxX = (event.clientX > Mouse.boundries.maxX) ? event.clientX : Mouse.boundries.maxX;
  Mouse.boundries.minX = (event.clientX < Mouse.boundries.minX) ? event.clientX : Mouse.boundries.minX;
  Mouse.boundries.maxY = (event.clientY > Mouse.boundries.maxY) ? event.clientY : Mouse.boundries.maxY;
  Mouse.boundries.minY = (event.clientY < Mouse.boundries.minY) ? event.clientY : Mouse.boundries.minY;
}

function getKeyDown(event) {
  // keydown creates a new record
  // TODO change char to be more generalized or exclude password fields.
  // Generalized would be char (a-Z), num (0-)), meta, arrows, tab, backsapce...
  // Goal is to not have password or very sensitive data in dataset but still be effective.
  let formId = event.target.form ? event.target.form.id : null;
  let inputId = event.target ? event.target.id : null;
  let char = event.key ? event.key : "No Key";
  // char No Key shows up on browser pretyped selects
  let input = { "keyDowntime":event.timeStamp,
                "formId": formId,
                "inputId":inputId,
                "dwell": null,
                "char": char };
//  console.log("KeyDown " +JSON.stringify(input, null, 2));
   DATA['FocusEvents'][currentFocus_GUID]['keyStrokes'].push(input);
}

function getKeyUp(event) {
  let timeStamp = event.timeStamp;
  // keyup closes the open record for that key.
  //Assumption:  You can not have two keydowns for the same key without a keyup inbetween
  for(var i = 0; i < DATA['FocusEvents'][currentFocus_GUID]['keyStrokes'].length; ++i) {
    if (DATA['FocusEvents'][currentFocus_GUID]['keyStrokes'][i].char == event.key &&
        DATA['FocusEvents'][currentFocus_GUID]['keyStrokes'][i].dwell == null ){

      DATA['FocusEvents'][currentFocus_GUID]['keyStrokes'][i].keyUpTime = timeStamp;
      DATA['FocusEvents'][currentFocus_GUID]['keyStrokes'][i].dwell = DATA['FocusEvents'][currentFocus_GUID]['keyStrokes'][i].keyUpTime - DATA['FocusEvents'][currentFocus_GUID]['keyStrokes'][i].keyDowntime;
//      DATA['FocusEvents'][currentFocus_GUID].activeTime += DATA['FocusEvents'][currentFocus_GUID]['keyStrokes'][i].dwell;
    }
  }
}

document.addEventListener('focusin', getFocusIn);
document.addEventListener('focusout', getFocusOut);
document.addEventListener('keydown', getKeyDown);
document.addEventListener('keyup', getKeyUp);
document.addEventListener('mousemove', moveMouse);
document.addEventListener('mousedown', downMouse);
document.addEventListener('mouseup', upMouse);
