function copy(id){
  var href = document.getElementById("href" + id.toString());
  var contentDiv = document.getElementById("originalclip_" + id.toString());
  var clipContent = "";
  if(href) {
    clipContent = href.textContent;
  } else if(contentDiv) {
    clipContent = contentDiv.textContent;
	
  }
	//clipContent = clipContent.replace(/&gt;/g, '>').replace(/&lt;/g, '<'); 
  if(clipContent) {
    copyHack(clipContent);
    var backgroundColor = document.body.style.backgroundColor;
    document.body.style.backgroundColor = '#ccffcc';
    setTimeout(function () {document.body.style.backgroundColor = backgroundColor}, 200);
  }
}



function copyHack(value){ //such a hack!!
    var copyTextarea = document.createElement("textarea");
    copyTextarea.style.position = "fixed";
    copyTextarea.style.opacity = "0";
    copyTextarea.textContent = value;
 
    document.body.appendChild(copyTextarea);
    copyTextarea.select();
    document.execCommand("copy");
    document.body.removeChild(copyTextarea);
}