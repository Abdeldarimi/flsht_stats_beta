// animate circles on load
window.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('circle[stroke-dasharray]').forEach(function(c){
        // trigger reflow
        const offset = c.getAttribute('stroke-dashoffset');
        c.style.strokeDashoffset = c.getAttribute('stroke-dasharray');
        setTimeout(()=>{ c.style.strokeDashoffset = offset; }, 50);
    });
});
