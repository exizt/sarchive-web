import { documentReady, formPreventEnterKey } from './base.js'

documentReady(function(){
    // form 태그 중 'prevent' 클래스를 가진 경우에 한해서 엔터키로 submit 방지.
    var forms = document.querySelectorAll("form.prevent")
    forms.forEach((element) => {
        formPreventEnterKey(element)
    })
});
