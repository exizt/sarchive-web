import { documentReady, formPreventEnterKey } from './base.js'

documentReady(function(){
    // form 태그 중 'prevent' 클래스를 가진 경우에 한해서 엔터키로 submit 방지.
    var forms = document.querySelectorAll("form.prevent")
    forms.forEach((element) => {
        formPreventEnterKey(element)
    })
    bindLabelAutoEvent()
});



/**
 * label 태그 클릭시 input 태그에 포커스 주는 함수
 *
 * label 태그와 input 태그가 인접해있을 경우,
 * label 태그 클릭시 자동으로 input 태그에 focus
 * html에서 label for=""와 input id="" 조합해서 기본적으로 사용할 수
 * 있는 기능이지만, id를 지정하기가 싫기 때문에 만든 함수
 * 크게 중요하지는 않으므로, async로 구현함.
 *
 * 사용법
 *   - label data-auto-click="true"를 두면 된다.
 */
async function bindLabelAutoEvent(){
    await autoLabelHTML()
    // console.log("[bindLabelAutoEvent] after")
    async function autoLabelHTML(){
        document.querySelectorAll('label[data-auto-click="true"]').forEach(el => {
            el.addEventListener("click", autoFocus)

            function autoFocus(){
                let nextEl = this.nextElementSibling
                if ( !!nextEl ){
                    if(nextEl.tagName.toLowerCase() == "input"){
                        this.nextElementSibling.focus()
                    }
                }
            }
        });
    }

}

