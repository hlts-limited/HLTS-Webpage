// Runs before the page paints. Marks that scripts are available so CSS can
// prepare reveal animations; without this class all content stays visible.
// If the animation script hasn't started within 3 seconds (blocked or failed),
// the class is removed again so nothing stays hidden.
(function () {
  var root = document.documentElement;
  root.classList.add('js');
  setTimeout(function () {
    if (!window.HLTSMotionReady) {
      root.classList.remove('js');
    }
  }, 3000);
})();
