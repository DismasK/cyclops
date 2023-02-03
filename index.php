<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="icon" href="images/bsk7I-hd.jpg"/>
<title>CYCLOPS</title>


<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css">

<script type="text/javascript" src="js/jquery_min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>

<script src="js/tensorflow_min.js"></script>
<script src="js/teachablemachine_image_min.js"></script>

<script type="text/javascript">
$(document).ready(function ()
{
	//Disable cut copy paste
	$('body').bind('cut copy paste', function(e)
	{
		e.preventDefault();
	});
	
	//Disable mouse right click
	$("body").on("contextmenu",function(e)
	{
		return false;
	});
});
</script>

</head>
<body>

<p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p>

<div class="app" align="center">
        
<button id="start-record-btn" class="btn-large" onClick="document.getElementById('btnVideoStart').click();">Start Recognition</button>

 
<p id="recording-instructions">Press the <strong>Start Recognition</strong> button and allow access to your webcam and microphone.</p>


</div>




<!-- ========================================================================================================================================================================================================================== -->

<div id="divVideo" hidden="hidden">
    <button type="button" onclick="init()" id="btnVideoStart">Start</button>
    
    <div id="webcam-container"></div>
    
    <div id="label-container"></div>
</div>




<script type="text/javascript">
// More API functions here:
// https://github.com/googlecreativelab/teachablemachine-community/tree/master/libraries/image

// the link to your model provided by Teachable Machine export panel
const URL = "tf_models/";

let model, webcam, labelContainer, maxPredictions;

// Load the image model and setup the webcam
async function init()
{
	const modelURL = URL + "model.json";
	const metadataURL = URL + "metadata.json";

	// load the model and metadata
	// Refer to tmImage.loadFromFiles() in the API to support files from a file picker
	// or files from your local hard drive
	// Note: the pose library adds "tmImage" object to your window (window.tmImage)
	model = await tmImage.load(modelURL, metadataURL);
	maxPredictions = model.getTotalClasses();

	// Convenience function to setup a webcam
	const flip = true; // whether to flip the webcam
	webcam = new tmImage.Webcam(200, 200, flip); // width, height, flip
	await webcam.setup(); // request access to the webcam
	await webcam.play();
	window.requestAnimationFrame(loop);

	// append elements to the DOM
	document.getElementById("webcam-container").appendChild(webcam.canvas);
	labelContainer = document.getElementById("label-container");
	for (let i = 0; i < maxPredictions; i++)
	{ // and class labels
		labelContainer.appendChild(document.createElement("div"));
	}
}

async function loop()
{
	webcam.update(); // update the webcam frame
	await predict();
	window.requestAnimationFrame(loop);
}

// run the webcam image through the image model
async function predict()
{
	// predict can take in an image, video or canvas html element
	const prediction = await model.predict(webcam.canvas);
	for (let i = 0; i < maxPredictions; i++)
	{
		const classPrediction = prediction[i].className + ": " + prediction[i].probability.toFixed(2);
		labelContainer.childNodes[i].innerHTML = classPrediction;
	}
}
</script>




<!-- ========================================================================================================================================================================================================================== -->




        
<script>
try
{
	var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
	var recognition = new SpeechRecognition();
}
catch(e)
{
	console.error(e);
	$('.no-browser-support').show();
	$('.app').hide();
}


var noteTextarea = $('#note-textarea');
var instructions = $('#recording-instructions');
var notesList = $('ul#notes');

var noteContent = '';

// Get all notes from previous sessions and display them.




/*-----------------------------
      Voice Recognition 
------------------------------*/

// If false, the recording will stop after a few seconds of silence.
// When true, the silence period is longer (about 15 seconds),
// allowing us to keep recording even when the user pauses. 
recognition.continuous = true;

// This block is called every time the Speech APi captures a line. 
recognition.onresult = function(event)
{
	// event is a SpeechRecognitionEvent object.
	// It holds all the lines we have captured so far. 
	// We only need the current one.
	var current = event.resultIndex;
	
	// Get a transcript of what was said.
	transcript = event.results[current][0].transcript;

	//alert(transcript);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

const settings = {
async: true,
crossDomain: true,
url: 'https://api.cohere.ai/classify',
method: 'POST',
headers:
{
	accept: 'application/json',
	'Cohere-Version': '2022-12-06',
	'content-type': 'application/json',
	authorization: 'Bearer <API_KEY>'
},
processData: false,
data: '{"inputs":["' + transcript + '"],\
\
"examples":[\
{"text":"Do you offer same day shipping?","label":"Shipping and handling policy"},\
{"text":"Can you ship to Italy?","label":"Shipping and handling policy"},\
{"text":"How long does shipping take?","label":"Shipping and handling policy"},\
{"text":"Can I buy online and pick up in store?","label":"Shipping and handling policy"},\
{"text":"What are your shipping options?","label":"Shipping and handling policy"},\
{"text":"My order arrived damaged, can I get a refund?","label":"Start return or exchange"},\
{"text":"You sent me the wrong item","label":"Start return or exchange"},\
{"text":"I want to exchange my item for another colour","label":"Start return or exchange"},\
{"text":"I ordered something and it wasn\'t what I expected. Can I return it?","label":"Start return or exchange"},\
{"text":"What\'s your return policy?","label":"Start return or exchange"},\
{"text":"Where\'s my package?","label":"Track order"},\
{"text":"When will my order arrive?","label":"Track order"},\
{"text":"What\'s my shipping number?","label":"Track order"},\
{"text":"How are you doing","label":"Am very fine, what about you?"},\
{"text":"How are you","label":"Am very fine, what about you?"}\
],\
\
"truncate":"END","model":"large"}'
};

$.ajax(settings).done(function (response)
{
	console.log(response);
	
	
	data = JSON.stringify(response);
	
	
	var subString = data.match('prediction":"(.*)labels');
	
	var sub_1 = subString[1];
	
	var result = sub_1.slice(0, -27);
	
	document.getElementById("txtSpeechSynthesis").value = result;
	
	
	
	document.getElementById("play").click();
});
	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

};

recognition.onstart = function()
{ 

  instructions.text('Voice recognition activated. Try speaking into the microphone.');
}

recognition.onspeechend = function()
{
  instructions.text('You were quiet for a while so voice recognition turned itself off.');
}

recognition.onerror = function(event)
{
  if(event.error == 'no-speech')
  {
    instructions.text('No speech was detected. Try again.');  
  };
}


/*-----------------------------
      App buttons and input 
------------------------------*/

$('#start-record-btn').on('click', function(e)
{
	if (noteContent.length)
	{
		document.getElementById('note-textarea').value ='';
		noteContent.length = '';
	}
	recognition.start();
});
</script>


<!-- ========================================================================================================================================================================================================================== -->


<form hidden="hidden">

    <input type="text" class="txt" id="txtSpeechSynthesis">
    
    <div>
        <label for="rate">Rate</label><input type="range" min="0.5" max="2" value="0.5" step="0.1" id="rate">
        
        <div class="rate-value">1</div>
        <div class="clearfix"></div>
    </div>
    
    <div>
        <label for="pitch">Pitch</label><input type="range" min="0" max="2" value="1" step="0.1" id="pitch">
        <div class="pitch-value">1</div>
        <div class="clearfix"></div>
      
      
        <select>
            <option data-lang="en-US" data-name="Microsoft Anna - English (United States)">Microsoft Anna - English (United States) (en-US) -- DEFAULT</option>
        </select>
    </div>
    
    <div class="controls">
        <button id="play" type="submit">Play</button>
    </div>
</form>

<script>
var synth = window.speechSynthesis;

var inputForm = document.querySelector('form');
var inputTxt = document.querySelector('.txt');
var voiceSelect = document.querySelector('select');

var pitch = document.querySelector('#pitch');
var pitchValue = document.querySelector('.pitch-value');
var rate = document.querySelector('#rate');
var rateValue = document.querySelector('.rate-value');

var voices = [];



function speak()
{
    if (synth.speaking)
	{
        console.error('speechSynthesis.speaking');
        return;
    }
    if (inputTxt.value !== '')
	{
    var utterThis = new SpeechSynthesisUtterance(inputTxt.value);
    utterThis.onend = function (event)
	{
        console.log('SpeechSynthesisUtterance.onend');
    }
    utterThis.onerror = function (event)
	{
        console.error('SpeechSynthesisUtterance.onerror');
    }
    var selectedOption = voiceSelect.selectedOptions[0].getAttribute('data-name');
    for(i = 0; i < voices.length ; i++)
	{
      if(voices[i].name === selectedOption)
	  {
        utterThis.voice = voices[i];
        break;
      }
    }
    utterThis.pitch = pitch.value;
    utterThis.rate = rate.value;
    synth.speak(utterThis);
  }
}

inputForm.onsubmit = function(event)
{
  event.preventDefault();

  speak();

  inputTxt.blur();
}

pitch.onchange = function()
{
  pitchValue.textContent = pitch.value;
}

rate.onchange = function()
{
  rateValue.textContent = rate.value;
}

voiceSelect.onchange = function()
{
  speak();
}
</script>


<!-- ========================================================================================================================================================================================================================== -->

<p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p>


</body>
</html>






