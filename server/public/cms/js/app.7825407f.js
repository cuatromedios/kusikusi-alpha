(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([[1],{0:function(e,t,n){e.exports=n("2f39")},"2f39":function(e,t,n){"use strict";n.r(t);var a=n("967e"),r=n.n(a),s=(n("a481"),n("96cf"),n("fa84")),o=n.n(s),i=(n("7d6e"),n("e54f"),n("573e"),n("985d"),n("31cd"),n("6ba9"),n("2b0e")),u=n("1f91"),l=n("42d2"),c=n("b05d"),p=n("18d6"),d=n("2a19");i["a"].use(c["a"],{config:{},lang:u["a"],iconSet:l["a"],plugins:{LocalStorage:p["a"],Notify:d["a"]}});var f=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{attrs:{id:"q-app"}},[e.prepared?e._e():n("div",{staticClass:"q-ma-xl flex justify-center"},[n("q-spinner",{attrs:{size:"xl"}})],1),e.prepared?n("router-view"):e._e()],1)},m=[],g=(n("7f7f"),{name:"Kusikusi",data:function(){return{prepared:!1}},created:function(){var e=this;return o()(r.a.mark((function t(){var n,a;return r.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e.$api.baseURL="/api",t.next=3,e.$api.get("/cms/config");case 3:return n=t.sent,n.success&&e.$store.commit("setConfig",n.data),t.next=7,e.$store.dispatch("getLocalSession");case 7:if(!e.$store.getters.hasAuthtoken){t.next=14;break}return t.next=10,e.$api.get("/user/me");case 10:a=t.sent,a.status>=400&&"login"!==e.$route.name?(e.prepared=!0,e.$router.push({name:"login"})):(e.$store.commit("setUser",a.data),e.prepared=!0),t.next=16;break;case 14:e.prepared=!0,"login"!==e.$route.name&&e.$router.push({name:"login"});case 16:case"end":return t.stop()}}),t)})))()}}),h=g,b=n("2877"),v=n("eebe"),w=n.n(v),k=n("0d59"),y=Object(b["a"])(h,f,m,!1,null,null,null),x=y.exports;w()(y,"components",{QSpinner:k["a"]});var _=n("4bde"),C=n("2f62"),L=(n("28a5"),n("f559"),n("2ef0")),S=n.n(L),$={config:{},lang:"en",uiLang:null,currentTitle:"",loading:!1,toolbar:{editButton:!1,saveButton:!1},menuItems:{dashboard:{label:"dashboard.title",icon:"dashboard",name:"dashboard"},content:{label:"content.title",icon:"home",name:"content"},media:{label:"media.title",icon:"photo",name:"media"},users:{label:"users.title",icon:"supervised_user_circle",name:"users"},configuration:{label:"settings.title",icon:"settings_applications",name:"settings"},logout:{label:"login.logout",icon:"exit_to_app",name:"login"}}},P={langs:function(e){return S.a.get(e,"config.langs",[])},defaultLang:function(e){return e.config&&e.config.langs?e.config.langs[0]:""},menu:function(e,t,n){var a=S.a.clone(S.a.get(e,"config.menu.".concat(n.session.user.profile)));return a||(a=(n.session.user.profile,[e.menuItems.content])),a.push(e.menuItems.logout),a},iconOf:function(e){return function(t){return S.a.get(e,"config.models[".concat(t,"].icon"),"blur_on")}},nameOf:function(e){return function(t){return S.a.get(e,"config.models[".concat(t,"].name"),t)}}},U={getCmsConfig:function(e){return o()(r.a.mark((function t(){var n,a,s;return r.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return n=e.commit,t.next=3,i["a"].prototype.$api.get("/config/cms");case 3:a=t.sent,n("setCms",a.result),s=p["a"].getItem("uiLang"),s&&""!==s||(s=a.result.langs[0]||"en"),n("setUiLang",s);case 8:case"end":return t.stop()}}),t)})))()}},E={setConfig:function(e,t){for(var n in S.a.set(t,"langs",S.a.get(t,"langs",["en"])),e.lang=t.langs[0],S.a.get(t,"models",[]))for(var a in S.a.get(t,"models[".concat(n,"].form"),[])){var r=[];for(var s in S.a.get(t,"models[".concat(n,"].form[").concat(a,"].components"),[])){var o=S.a.get(t,"models[".concat(n,"].form[").concat(a,"].components[").concat(s,"]"));if(S.a.startsWith(o.value,"contents."))for(var i in t.langs){var u=S.a.cloneDeep(o);u.value+=".".concat(t.langs[i]),u.isMultiLang=!0,u.props={lang:t.langs[i],field:u.value.split(".")[1]},r.push(u)}else o.isMultiLang=!1,r.push(o)}S.a.set(t,"models[".concat(n,"].form[").concat(a,"].components"),r)}e.config=t},setTitle:function(e,t){e.currentTitle=t},setLang:function(e,t){p["a"].set("lang",t),e.lang=t,i["a"].i18n.set(t)},setUiLang:function(e,t){p["a"].set("uiLang",t),e.uiLang=t},setEditButton:function(e,t){e.toolbar.editButton=t},setSaveButton:function(e,t){e.toolbar.saveButton=t}},T={namespaced:!1,state:$,getters:P,actions:U,mutations:E},A={user:{profile:""},authtoken:""},B={hasAuthtoken:function(e){return""!==e.authtoken},entitiesWithWritePermissions:function(e){for(var t=[],n=0;n<S.a.get(e,"user.permissions.length",0);n++)if("none"!==e.user.permissions[n].write&&"none"!==e.user.permissions[n].read){var a=e.user.permissions[n].entity;a.write=e.user.permissions[n].write,a.read=e.user.permissions[n].read,t.push(a)}return t},entitiesWithPermissions:function(e){for(var t=[],n=0;n<S.a.get(e,"user.permissions.length",0);n++)if("none"!==e.user.permissions[n].read){var a=e.user.permissions[n].entity;a.write=e.user.permissions[n].write,a.read=e.user.permissions[n].read,t.push(a)}return t}},I={getLocalSession:function(e){var t=e.dispatch,n=e.commit,a=p["a"].getItem("kusikusi_session");return a&&a!=={}?(n("setAuthtoken",a.authtoken),n("setUser",a.user)):t("resetUserData"),a},resetUserData:function(e){var t=e.commit;t("setAuthtoken",""),t("setUser",{})}},R={setAuthtoken:function(e,t){e.authtoken=t,i["a"].prototype.$api.setAuthorization(t);var n=p["a"].getItem("kusikusi_session")||{};n.authtoken=t,p["a"].set("kusikusi_session",n)},setUser:function(e,t){e.user=t}},j={namespaced:!1,state:A,getters:B,actions:I,mutations:R},O={blankEntity:{id:"",model:"",view:"",contents:[],entity_relations:[],parent_entity_id:"",is_active:!0,properties:{},published_at:null,unpublished_at:null,version:0,version_full:0,version_relations:0,version_tree:0,created_at:null,updated_at:null,updated_by:null}},D={namespaced:!1,state:O},z=Object(_["store"])((function({Vue:e}){e.use(C["a"]);const t=new C["a"].Store({modules:{ui:T,session:j,content:D},strict:!1});return t})),V=n("8c4f");const M=[{path:"/",component:()=>n.e(7).then(n.bind(null,"fd28")),redirect:{name:"login"},children:[{path:"/login",component:()=>Promise.all([n.e(0),n.e(4)]).then(n.bind(null,"013f")),name:"login"}]},{path:"/panel",component:()=>Promise.all([n.e(0),n.e(5)]).then(n.bind(null,"8071")),children:[{path:"/content/:entity_id?/:model?/:conector?/:parent_entity_id?",component:()=>Promise.all([n.e(0),n.e(3)]).then(n.bind(null,"cf8e")),name:"content"}]}];M.push({path:"*",component:()=>n.e(6).then(n.bind(null,"e51e"))});var q=M,F=Object(_["route"])((function({Vue:e}){e.use(V["a"]);const t=new V["a"]({scrollBehavior:()=>({x:0,y:0}),routes:q,mode:"history",base:"/cms/"});return t})),W=function(){return Q.apply(this,arguments)};function Q(){return Q=o()(r.a.mark((function e(){var t,n,a;return r.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:if("function"!==typeof z){e.next=6;break}return e.next=3,z({Vue:i["a"]});case 3:e.t0=e.sent,e.next=7;break;case 6:e.t0=z;case 7:if(t=e.t0,"function"!==typeof F){e.next=14;break}return e.next=11,F({Vue:i["a"],store:t});case 11:e.t1=e.sent,e.next=15;break;case 14:e.t1=F;case 15:return n=e.t1,t.$router=n,a={el:"#q-app",router:n,store:t,render:function(e){return e(x)}},e.abrupt("return",{app:a,store:t,router:n});case 19:case"end":return e.stop()}}),e)}))),Q.apply(this,arguments)}var H={general:{title:"Kusikusi",subtitle:"By Cuatromedios",pullToRefresh:"Pull to refresh",releaseToRefresh:"release to refresh",refreshing:"Refreshing",serverError:"There was an error in the server",back:"Back",edit:"Edit",save:"Save",cancel:"Cancel",confirm:"Confirm",ok:"Ok",sure:"Are you sure?",delete:"Delete",add:"Add"},login:{title:"Login",logout:"Logout",email:"Email",password:"Password",button:"Login",loginInvalid:"Invalid credentials",loginError:"Can't connect to the server",invalidEmail:"Please enter a valid email",invalidPassword:"Please enter a password"},dashboard:{},contents:{icon:"ballot",view:"Vista",contents:"Contents",publication:"Publication",active:"Published",delete:"Do you really want to delete this entity?",title:"Title",summary:"Summary",url:"Friendly url",footer:"Footer",publishedAt:"Publish at",unpublishedAt:"Stop publishing at"},media:{title:"Media",singular:"medium",uploader:"Media uploader",upload:"Upload file",select:"Select or drop file...",replace:"Replace file",tags:"Tags",tag:"tag",details:"Details",filename:"Original file name",format:"Format",mimetype:"Mime Type",size:"Size",url:"External URL",lang:"Lenguaje del medio",status:{title:"Status",idle:"Idle",failed:"Failed",uploading:"Uploading",uploaded:"Uploaded"}},qr:{title:"QR Code",print:"Print"},users:{title:"Users"},settings:{title:"Settings"},security:{any:"any",own:"own",none:"none"}},J={general:{title:"",subtitle:"By Cuatromedios",pullToRefresh:"Desliza para recargar",releaseToRefresh:"Suelta para recargar",refreshing:"Recargando",serverError:"Hubo un error inesperado en el servidor",back:"Regresar"},login:{title:"Ingresar",welcome:"¡Bienvenido!",email:"Correo Electrónico",password:"Contraseña",button:"Ingresar",invalid:"El correo electrónico o la contraseña son incorrectas"},layout:{dashboard:"Tablero",content:"Contenido",media:"Medios",users:"Usuarios",configuration:"Configuración",logout:"Cerrar sesión"},formComponents:{name:"Nombre",title:"Título",description:"Descripción",subtitle:"Subtítiulo",uploadFiles:"Subir archivos"},content:{update:"Actualizar",saveChild:"Crear hijo",delete:"Eliminar",children:"Hijos",saveEntitySuccess:"actualizada exitosamenta",media:"Agregar"}},K={"en-us":H,"es-mx":J},N=n("a925");i["a"].use(N["a"]);const G=new N["a"]({locale:"en-us",fallbackLocale:"en-us",messages:K,silentTranslationWarn:!0});var X=Object(_["boot"])(({app:e})=>{e.i18n=G}),Y=n("bc3a"),Z=n.n(Y),ee=Object(_["boot"])(({Vue:e})=>{e.prototype.$axios=Z.a}),te=n("c583"),ne=n("64c8"),ae=n("4cf5");function re(){return se.apply(this,arguments)}function se(){return se=o()(r.a.mark((function e(){var t,n,a,s,o,u,l,c,p;return r.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,W();case 2:t=e.sent,n=t.app,a=t.store,s=t.router,o=!0,u=function(e){o=!1,window.location.href=e},l=window.location.href.replace(window.location.origin,""),c=[X,ee,te["default"],ne["a"],ae["a"]],p=0;case 11:if(!(!0===o&&p<c.length)){e.next=29;break}if("function"===typeof c[p]){e.next=14;break}return e.abrupt("continue",26);case 14:return e.prev=14,e.next=17,c[p]({app:n,router:s,store:a,Vue:i["a"],ssrContext:null,redirect:u,urlPath:l});case 17:e.next=26;break;case 19:if(e.prev=19,e.t0=e["catch"](14),!e.t0||!e.t0.url){e.next=24;break}return window.location.href=e.t0.url,e.abrupt("return");case 24:return console.error("[Quasar] boot error:",e.t0),e.abrupt("return");case 26:p++,e.next=11;break;case 29:if(!1!==o){e.next=31;break}return e.abrupt("return");case 31:new i["a"](n);case 32:case"end":return e.stop()}}),e,null,[[14,19]])}))),se.apply(this,arguments)}re()},"31cd":function(e,t,n){}},[[0,2,0]]]);