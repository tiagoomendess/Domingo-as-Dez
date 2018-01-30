﻿CKEDITOR.dialog.add("googledocs",function(a){return{title:a.lang.googledocs.title,width:400,height:350,onLoad:function(){getDocuments()},contents:[{id:"settingsTab",label:a.lang.googledocs.settingsTab,elements:[{type:"select",id:"documents",className:"googledocs",label:a.lang.googledocs.selectDocument,items:[],"default":"",size:4,onChange:function(){CKEDITOR.dialog.getCurrent().getContentElement("settingsTab","txtUrl").setValue(this.getValue())}},{type:"text",id:"txtUrl",label:a.lang.googledocs.url,
required:!0,validate:CKEDITOR.dialog.validate.notEmpty(a.lang.googledocs.alertUrl)},{type:"hbox",widths:["60px","330px"],className:"googledocs",children:[{type:"text",width:"45px",id:"txtWidth",label:a.lang.common.width,"default":710,required:!0,validate:CKEDITOR.dialog.validate.integer(a.lang.googledocs.alertWidth)},{type:"text",id:"txtHeight",width:"45px",label:a.lang.common.height,"default":920,required:!0,validate:CKEDITOR.dialog.validate.integer(a.lang.googledocs.alertHeight)}]}]},{id:"uploadTab",
label:a.lang.googledocs.uploadTab,filebrowser:"uploadButton",elements:[{type:"file",id:"upload"},{type:"fileButton",id:"uploadButton",label:a.lang.googledocs.btnUpload,filebrowser:{action:"QuickUpload",onSelect:function(a){getDocuments(a)}},"for":["uploadTab","upload"]}]}],onOk:function(){var b=a.document.createElement("iframe"),c=encodeURIComponent(this.getValueOf("settingsTab","txtUrl"));b.setAttribute("src","http://docs.google.com/viewer?url="+c+"&embedded=true");b.setAttribute("width",this.getValueOf("settingsTab",
"txtWidth"));b.setAttribute("height",this.getValueOf("settingsTab","txtHeight"));b.setAttribute("style","border: none;");a.insertElement(b)},onShow:function(){getDocuments()}}});
var getDocuments=function(a){CKEDITOR.env.ie7Compat&&fixIE7display();$.get(CKEDITOR.currentInstance.config.filebrowserGoogledocsBrowseUrl,function(b){var c=CKEDITOR.dialog.getCurrent().getContentElement("settingsTab","documents");c.clear();$.each(b,function(a,b){c.add(b.name,b.url)});c.setValue(a);console.log(a);a&&c.focus()},"json")};