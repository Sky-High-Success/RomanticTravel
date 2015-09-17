var I_S = jQuery;
I_S(document).ready(function(){
var runningRequest = false;
    var request;

    I_S('#I_S_Q').keyup(function(e){
        e.preventDefault();
        var I_Sq = I_S(this);

        if(I_Sq.val() == ''){
            I_S('div#results').html('');
            return false;
        }

        //Abort opened requests to speed it up
        if(runningRequest){
            request.abort();
        }

        runningRequest=true;
        I_S_Loader_show();


// This needs to be changed to the location of the search.php file

        request = I_S.getJSON(instant.AjaxUrl,{
	   I_S_Q:I_Sq.val(),action: 'i_s_magic'
        },function(data){           
            I_S_showResults(data,I_Sq.val());
            runningRequest=false;
        });

function I_S_showResults(data, highlight){
           var resultHtml = '';
            I_S.each(data, function(i,item){
                resultHtml+='<div class="result">';
                resultHtml+='<h2>'+item.title+'</h2>';
                resultHtml+='<p>'+item.content.replace(highlight, '<span class="highlight">'+highlight+'</span>')+'</p>';
                resultHtml+='<a href="'+ item.url +'" class="readMore">' +instant.read_more + '</a>';
                resultHtml+='</div>';
            });

            I_S('div#results').html(resultHtml);
            I_S_Loader_hide();
        }

function I_S_Loader_show(){
	I_S('#I_S_ajax_loader').show("slow");
}
function I_S_Loader_hide(){
	I_S('#I_S_ajax_loader').hide("3000");
}

        I_S('#I_S_form').submit(function(e){
            e.preventDefault();
        });
    });
 });