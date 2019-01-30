//contenteditable=\"true\"
//function (select) {
//    
//}

$(document).ready(function() {
    BalloonEditor
	.create( document.querySelector( '#editor' ) )
	.then( editor => {
            console.log( editor );
	} )
	.catch( error => {
            console.error( error );
	} );
});
