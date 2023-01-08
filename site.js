function copy(id){
  var contentDiv = document.getElementById("clip" + id.toString());
  if(contentDiv) {
    var clipContent = contentDiv.innerHTML;
    copyHack(clipContent);
    var backgroundColor = document.body.style.backgroundColor;
    document.body.style.backgroundColor = '#6666ee';
    setTimeout(function () {document.body.style.backgroundColor = backgroundColor}, 400);
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