function copy(id){
  let href = document.getElementById("href" + id.toString());
  let contentDiv = document.getElementById("originalclip_" + id.toString());
  let clipContent = "";
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
    let copyTextarea = document.createElement("textarea");
    copyTextarea.style.position = "fixed";
    copyTextarea.style.opacity = "0";
    copyTextarea.textContent = value;
 
    document.body.appendChild(copyTextarea);
    copyTextarea.select();
    document.execCommand("copy");
    document.body.removeChild(copyTextarea);
}

function gotoSelectedClipType(){
  if(document.getElementById("clip") && document.getElementById("clip").value.trim() == "") {
    let select = document.getElementById("clipboard_item_type_id");
    window.location = "?type_id=" + select[select.selectedIndex].value;
  }
}



function changeClipType(clipboardItemId, hashedEntities, jsId) {
  console.log(jsId);
    let select = document.getElementById(jsId);
    let value = select[select.selectedIndex].value;
      const payload = {
        action: "update",
        mode: "crud",
        value: value,
        column: "type_id",
        table: "clipboard_item",
        pk: "clipboard_item_id",
        clipboard_item_id: clipboardItemId,
        //pre_entity: table + "+" + pkName + "+" + pkValue,
        hashed_entities: hashedEntities
      };
      
      const xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            // If your PHP returns JSON:
            // const data = JSON.parse(xhr.responseText);
            //console.log("Server replied:", xhr.responseText);
            //window.location.reload();
            //no action necessary
          } else {
            console.error("Request failed:", xhr.status, xhr.statusText);
          }
        }
      };
      xhr.open("POST", "", true);
      // Tell the server we?re sending JSON
      xhr.setRequestHeader("Content-Type", "application/json");
      xhr.send(JSON.stringify(payload));
    
}

