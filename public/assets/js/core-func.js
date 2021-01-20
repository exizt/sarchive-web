function wrapData(v,d){
    return wrap(typeof v === "undefined") ? d : v

    function wrap(v){
        return (typeof v === "null" || typeof v === "undefined") ? undefined : v
    }
}
