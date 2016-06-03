<div class="row">
<div class="col-xs-12">
<div class="panel panel-primary">
<div class="panel-heading">Recordings List
</div>
<div class="panel-body" style="padding:0; margin:0; position:relative">
  <div id="loading-overlay"></div>
        <div class="row" style="padding:0; margin:0;">
        <div class="col-xs-12">
        <div id="view-container">                <img class="table-loading"
                     src='<?php echo base_url() ?>assets/img/ajax-loader-bar.gif'>
            </div>
            </div>
 
        </div>
    </div>

</div>
</div>
</div>
</div>
<style>
body { }
.loading-overlay {  position:absolute; width:100%; height:100%; background:#000; opacity: 0.4; filter: alpha(opacity=40); z-index:10; top:0; left:0 }
.container-fluid { }
.top-row { padding:10px 10px 0; }
.bottom-row { padding:0px 10px 10px; }
.panel-body { overflow:hidden }
#view-container { margin:0; padding:0 0px; overflow-y:auto; height:100%; overflow-x:hidden; }
</style>

<script type="text/javascript">
var process_url = 'recordings/process_view';
var page_name ="recordings";
var table_columns = <?php echo json_encode($columns) ?>; //the columns in this view

    $(document).ready(function () {
        view.init();
        view.has_filter = false;
		 $('#view-container').on('click', 'tr[data-path]', function (e) {
                e.preventDefault();
                modal.convert_recording($(this), $(this).attr('data-id'), $(this).attr('data-path'))
            })
    });
	
var modal = {
	  convert_recording: function ($btn, id, path) {
            $.ajax({
                url: helper.baseUrl + 'recordings/listen/' + id + '/' + path,
                type: "POST",
                dataType: "JSON",
                beforeSend: function () {
					 var mheader = 'Call Playback';
        var mbody = '<div id="waveform"><img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /></div><div class="controls" style="display:none"><button class="btn btn-primary" id="playpause"><i class="glyphicon glyphicon-pause"></i>Pause</button> <button class="btn btn-primary" id="slowplay"><i class="glyphicon glyphicon-left"></i>Slower</button> <button class="btn btn-primary" id="speedplay"><i class="glyphicon glyphicon-right"></i>Faster</button> <a target="blank" class="btn btn-info btn-download" href="">Download</a> <span class="pull-right" id="duration"></span> <span id="audiorate" class="hidden">1</span></div>';
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
        modals.load_modal(mheader, mbody, mfooter);
                }
            }).fail(function(){
				 flashalert.danger("There was a problem loading the recording");
			}).done(function (response) {
                modal.call_player(response.filename, response.filetype)
            });
        },
 call_player: function (url, filetype) {
	 	$('#waveform').empty();
	 	$modal.find('.btn-download').attr('href',url.replace("ogg", "mp3"));
	 	$modal.find('.controls').show();
        $modal.one("click", ".close-modal,.close", function () {
            wavesurfer.destroy();
			wavesurfer.destroy();
            modal_body.empty();
        });
        modal.wavesurfer(url.replace('ogg', 'mp3'));
    },
    wavesurfer: function (fileurl) {
        // Create an instance
        wavesurfer = Object.create(WaveSurfer);
        // Init & load audio file
        var options = {
            container: document.querySelector('#waveform'),
            waveColor: 'violet',
            progressColor: 'purple',
            loaderColor: 'purple',
            cursorColor: 'navy',
            audioRate: 1
        };
        if (location.search.match('scroll')) {
            options.minPxPerSec = 100;
            options.scrollParent = true;
        }
        if (location.search.match('normalize')) {
            options.normalize = true;
        }
        // Init
        wavesurfer.init(options);
        // Load audio from URL
        wavesurfer.load(fileurl);
        // Regions
        if (wavesurfer.enableDragSelection) {
            wavesurfer.enableDragSelection({
                color: 'rgba(0, 255, 0, 0.1)'
            });
        }
        ;
        // Play at once when ready
        // Won't work on iOS until you touch the page
        wavesurfer.on('ready', function () {
            wavesurfer.play();
            $('#duration').text(wavesurfer.getDuration() + 's');
        });
        // Report errors
        wavesurfer.on('error', function (err) {
            console.error(err);
        });
        // Do something when the clip is over
        wavesurfer.on('finish', function () {
            console.log('Finished playing');
        });

          $modal.on('click','#speedplay',function () {
            wavesurfer.setPlaybackRate(Number($('#audiorate').text()) + 0.2);
            $('#audiorate').text(Number($('#audiorate').text()) + 0.2);
        });
         $modal.on('click','#slowplay',function () {
            wavesurfer.setPlaybackRate(Number($('#audiorate').text()) - 0.2);
            $('#audiorate').text(Number($('#audiorate').text()) - 0.2);
        });
        $modal.on('click','#playpause',function () {
            if ($('#playpause i').hasClass('glyphicon-pause')) {
                $('#playpause').html('<i class="glyphicon glyphicon-play"></i> Play');
                wavesurfer.pause();
                console.log("Paused");
            } else {
                $('#playpause').html('<i class="glyphicon glyphicon-pause"></i> Pause');
                wavesurfer.play();
                console.log("Playing");
            }
        });
    }
}
	

</script>