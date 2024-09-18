/**
 * UI Initiative Shutters Slider
 *
 * Infinite 3D carousel slider
 *
 * https://uiinitiative.com
 *
 * Copyright 2022 UI Initiative
 *
 * Released under the UI Initiative Regular License
 *
 */

!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e="undefined"!=typeof globalThis?globalThis:e||self).EffectShutters=t()}(this,(function(){"use strict";return"undefined"!=typeof window&&window.SwiperElementRegisterParams&&window.SwiperElementRegisterParams(["shuttersEffect"]),function({swiper:e,extendParams:t,on:s}){t({shuttersEffect:{split:5}}),s("beforeInit",(()=>{if("shutters"!==e.params.effect)return;e.classNames.push("swiper-shutters");const t={watchSlidesProgress:!0,parallax:{enabled:!0}};Object.assign(e.params,t),Object.assign(e.originalParams,t)})),s("init",(()=>{"shutters"===e.params.effect&&e.slides.forEach((t=>{const s=t.querySelector(".swiper-shutters-image");if(!s)return;const r=s.nextElementSibling,i=document.createElement("div");i.classList.add("swiper-shutters-image-clones");for(let t=0;t<e.params.shuttersEffect.split;t+=1){const e=document.createElement("div");e.classList.add("swiper-shutters-image-clone"),e.appendChild(s.cloneNode()),i.appendChild(e)}r?s.parentNode.insertBefore(i,r):s.parentNode.appendChild(i)}))})),s("resize init",(()=>{"shutters"===e.params.effect&&(e.el.querySelectorAll(".swiper-shutters-image").forEach((t=>{t.style.width=`${e.width}px`,t.style.height=`${e.height}px`})),e.el.querySelectorAll(".swiper-slide, swiper-slide").forEach((t=>{t.querySelectorAll(".swiper-shutters-image-clone").forEach(((t,s)=>{const r=100/e.params.shuttersEffect.split,i=t.querySelector(".swiper-shutters-image");"vertical"===e.params.direction?(t.style.height=100/e.params.shuttersEffect.split+"%",t.style.top=100/e.params.shuttersEffect.split*s+"%",i.style.top=`-${100*s}%`):(t.style.width=100/e.params.shuttersEffect.split+"%",t.style.left=100/e.params.shuttersEffect.split*s+"%",i.style.left=`-${100*s}%`),i.setAttribute("data-swiper-parallax","10%"),t.setAttribute("data-swiper-parallax",r*(s+1)*(s%2==0?.5:-1)+"%")}))})))}))}}));
//# sourceMappingURL=effect-shutters.min.js.map