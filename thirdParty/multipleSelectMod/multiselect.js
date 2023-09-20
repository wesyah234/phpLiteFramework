/*
	coded by Kae - kae@verens.com
	I'd appreciate any feedback.
	You have the right to include this in your sites.
	Please retain this notice.
*/

/* edit these variables to customise the multiselect */ {
	var show_toplinks=true;
}

/* global variables - do not touch */ {
	var isIE=window.attachEvent?true:false;
	var selectDefaults=[];
}
function addEvent(el,ev,fn){
	if(isIE)el.attachEvent('on'+ev,fn);
	else if(el.addEventListener)el.addEventListener(ev,fn,false);
}
function buildMultiselects(){
	do{
		found=0;
		a=document.getElementsByTagName('select');
		for(b=0;b<a.length,!found;b++){
			var ms=a[b];
			if(ms==null)break;
			var name=ms.name.replace(/\[\]$/,'');
			if(ms.multiple){
				/* common variables */ {
					selectDefaults[name]=[];
					var found=1,disabled=ms.disabled?1:0,width=ms.offsetWidth,height=ms.offsetHeight;
					if(width<120)width=120;
					if(height<60)height=60;
				}
				/* set up wrapper */ {
					var wrapper=document.createElement('div');
					wrapper.style.width=width+"px";
					wrapper.style.height=height+"px";
					wrapper.style.position='relative';
					wrapper.style.border="2px solid #000";
					wrapper.style.borderColor="#333 #ccc #ccc #333";
					wrapper.style.font="10px sans-serif";
					if(disabled)wrapper.style.background='#ddd';
				}
				if(show_toplinks){ /* reset, all, none */
					wrapper.appendChild(newLink("javascript:"+(disabled?"alert('selection disabled')":"multiselect_selectall('"+name+"','checked');"),'all'));
					wrapper.appendChild(document.createTextNode(', '));
					wrapper.appendChild(newLink("javascript:"+(disabled?"alert('selection disabled')":"multiselect_selectall('"+name+"','');"),'none'));
					wrapper.appendChild(document.createTextNode(', '));
					wrapper.appendChild(newLink("javascript:"+(disabled?"alert('selection disabled')":"multiselect_selectall('"+name+"','reset');"),'reset'));
				}
				/* setup multiselect */ {
					newmultiselect=document.createElement('div');
					newmultiselect.style.position='absolute';
					newmultiselect.style.top=show_toplinks?'15px':'0';
					newmultiselect.style.left='0';
					newmultiselect.style.overflow='auto';
					newmultiselect.style.width=(isIE?width-4:width)+"px";
					newmultiselect.style.height=show_toplinks?height-(isIE?19:15)+"px":height+'px';
				}
				c=ms.getElementsByTagName('option');
				for(d=0;d<c.length;d++){
					var label=document.createElement('label');
					label.style.display="block";
					label.style.border="1px solid #eee";
					label.style.borderWidth="1px 0";
					label.style.font="10px arial";
					label.style.lineHeight="10px";
					label.style.paddingLeft="20px";
					checkbox=document.createElement('input');
					checkbox.type="checkbox";
					if(c[d].selected){
						checkbox.checked="checked";
						checkbox.defaultChecked=true;
					}
					if(c[d].disabled){
						checkbox.disabled='disabled';
						label.style.color='#666';
					}
					selectDefaults[name][d]=c[d].selected?'checked':'';
					if(disabled)checkbox.disabled="disabled";
					checkbox.value=c[d].value;
					checkbox.style.marginLeft="-16px";
					checkbox.style.marginTop="-2px";
					checkbox.name=ms.name;

					// escape the label
					var text=c[d].innerHTML;
					text=text.replace(/\&nbsp;?/g,' ');
					text=text.replace(/\&lt;?/g,'<');
					text=text.replace(/\&gt;?/g,'>');

					labelText=document.createTextNode(text);
					label.appendChild(checkbox);
					label.appendChild(labelText);
					newmultiselect.appendChild(label);
				}
			wrapper.appendChild(newmultiselect);
			ms.parentNode.insertBefore(wrapper,ms);
			ms.parentNode.removeChild(ms);
			}
		}
	}while(found);
}
function multiselect_selectall(name,val){
	var els=document.getElementsByTagName('input'),found=0;
	for(var i=0;i<els.length;++i){
		if((els[i].name==name+'[]'||els[i].name==name)&&!els[i].disabled)els[i].checked=val=='reset'?selectDefaults[name][found++]:val;
	}
}
function newLink(href,text){
	var e=document.createElement('a');e.href=href;e.appendChild(document.createTextNode(text));return e;
}

addEvent(window,'load',buildMultiselects);
