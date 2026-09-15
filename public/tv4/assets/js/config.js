/* Paths shared by the local HTML preview and Laravel Blade pages. */
FF.paths={index:'/',shop:'/products',product:'/products/tao-xanh',cart:'/cart',checkout:'/checkout',orders:'/orders','order-detail':'/orders/NSX-20260912-0001',login:'/login',register:'/register',account:'/profile',addresses:'/addresses',review:'/reviews','ui-states':'/ui-states'};
FF.url=function(page, key){
 if(FF.staticPreview)return page+'.html';
 let p=FF.paths[page]||'/';
 if(page==='product' && key)p='/products/'+encodeURIComponent(key);
 if(page==='order-detail' && key)p='/orders/'+encodeURIComponent(key);
 return FF.base+p+(FF.preview?'?preview=1':'');
};
FF.asset=function(file){return FF.assetBase+'/'+file;};
