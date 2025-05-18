function doPost(e) {
  if(typeof e !== 'undefined'){
    return requestHandler(e);
  }  
}

function requestHandler(e){
  var res = handleRequest(e);
  return ContentService.createTextOutput(res);
}

function handleRequest(e) {

  var token = e.parameter.token;
  var method = e.parameter.method;
  var chat_id = e.parameter.chat_id;
  var topic_id = e.parameter.topic_id;
  var parse_mode = e.parameter.parse_mode;
  var text = e.parameter.text;
  
  var data = {
  'chat_id': chat_id,
  'parse_mode': parse_mode,
  'text' : text
  };
if (topic_id) {
  data['message_thread_id'] = topic_id; // Telegram uses `message_thread_id` for topics in groups
}
  var options = {
  'method' : 'post',
  'muteHttpExceptions': true,
  'payload' : data
  };

  var res = UrlFetchApp.fetch('https://api.telegram.org/bot' + token + '/' + method, options);
  return res.getContentText();
}