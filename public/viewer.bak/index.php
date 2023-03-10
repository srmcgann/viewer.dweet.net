<?
  if(!($url = $_GET['url'])) die();
?>
<!DOCTYPE html>
<html>
  <head>
    <style>
      html, body{
        margin: 0;
        background: #000;
        width: 100%;
        min-height: 100vh;
        overflow: hidden;
      }
      .loading{
        position: absolute;
        left: 50%;
        top: 50%;
        z-index: 10;
        display: inline-block;
        background-color: #0000;
        background-repeat: no-repeat;
        background-image: url(https://jsbot.cantelope.org/uploads/ZdQkX.gif);
        background-position: center center;
        background-size: 100% 100%;
        width: 400px;
        height: 400px;
        transform: translate(-50%, -50%);
      }
      .main{
        position: absolute;
        width:100%;
        height: 100%;
      }
    </style>
  </head>
  <body>
    <div class="loading"></div>
    <div class="main"></div>
    <script>
      fetchObj = sendData => {
        return {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }
      }
      let sendData = {
        url: '<?=$url?>'
      }
      var loading = document.querySelectorAll('.loading')[0]
      fetch('asset.php', fetchObj(sendData))
      .then(res=>res.blob()).then(data=>{
        type = ''
        if(data.type.indexOf('image')!=-1) type = 'image'
        if(data.type.indexOf('video')!=-1) type = 'video'
        if(data.type.indexOf('audio')!=-1) type = 'audio'
        let blob = data
        let url = URL.createObjectURL(blob)
        switch(type){
          case 'image':
            resource = new Image()
            resource.src = url
            resource.style.height="calc(100% - 20px)"
            resource.style.objectFit="contain"
            resource.style.width="calc(100% - 20px)"
            console.log('image detected')
          break
          case 'video':
            resource = document.createElement('video')
            resource.loop = true
            //resource.muted = true
            resource.style.maxHeight="calc(100% - 50px)"
            resource.style.height="calc(100% - 50px)"
            resource.style.width="calc(100% - 20px)"
            resource.style.objectFit="contain"
            console.log('video detected')
            resource.controls = true
            reader = new FileReader()
            reader.onload = function(e) {
              srcUrl = e.target.result
              resource.src = srcUrl
            }
            reader.readAsDataURL(blob)
          break
          case 'audio':
            resource = document.createElement('audio')
            resource.loop = true
            resource.style.position = 'absolute'
            resource.style.top = '50%'
            resource.style.left = '50%'
            resource.style.transform = "translate(-50%, -50%)"
            resource.controls = true
            console.log('audio detected')
            reader = new FileReader()
            reader.onload = function(e) {
              srcUrl = e.target.result
              resource.src = srcUrl
            }
            reader.readAsDataURL(blob)
          break
        }
        resource.style.margin="10px"
        resource.style.border="1px solid #fff3"
        loading.style.display='none'
        document.querySelector('.main').appendChild(resource)
      })
    </script>
  </body>
</html>
