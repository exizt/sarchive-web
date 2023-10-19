/* dom loaded event */
export var documentReady = function(f) {
    document.readyState == 'loading' ? document.addEventListener("DOMContentLoaded", f) : f();
};


export function formPreventEnterKey(form){
    form.addEventListener('keypress', function(e) {
        if (e.keyCode === 13) {
            e.preventDefault()
        }
    })
}
