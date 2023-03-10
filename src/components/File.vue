<template>
  <div
    class="fileDiv"
    :class="{'folderIcon': file.type=='folder', 'basicIcon': file.type!='folder' && state.loggedinUserBasicIcons}"
    ref="fileDiv"
  >
    <div
      class="dragHandle"
      ref="dragHandle"
      @mousedown="mousedown"
    ></div>
    <div v-if="0" class="fileButtons">
      <label :for="'privateCheckbox' + file.id" :key="'cblabel'+file.id" class="checkboxLabel" style="margin:2px;display:unset;transform: scale(.75);" :title="'toggle public visibility of this '+(+file.folder?'folder':'file')+`\n[`+file.name+' is currently '+(file.private==false?'PUBLIC':'PRIVATE')+']'">
        <input type="checkbox" :key="'cbkey'+file.id" :id="'privateCheckbox' + file.id" v-model="file.private" @input="togglePublic()">
        <span class="checkmark" :class="{'warning': file.private==false}" style=";border: 1px solid #fff8"></span>
      </label>
      <button @click="renameFile()" :title="'rename'" class="fileButton renameButton"></button>
      <button v-if="file.type != 'folder'" @click="downloadFile()" :title="'download file'" class="fileButton downloadButton"></button>
      <button @click="deleteFile()" :title="'delete'" class="fileButton deleteButton"></button>
    </div>
    <div
      @click="load()"
      class="file"
      :ref="file.id"
      :title="`view ${file.name}`"
    >
      <div class="fileName" v-html="fileName" :ref="'name_'+file.hash"></div>
    </div>
    <a
      v-if="(file.type == 'folder' || viewableAsset()) && !file.private"
      class="fileName shareLink"
      :title="'share link (for: '+file.name+')\nright+click to copy'"
      v-html="'share'"
      :ref="'share_'+file.hash"
      :href="state.shareURL + '/' + state.decToAlpha(file.id)"
      target="_blank"
    ></a>
  </div>
</template>

<script>

export default {
  name: 'File',
  props: ['state', 'file', 'dropzone'],
  data(){
    return {
    }
  },
 computed:{
    fileName(){
      let name = this.file.name
      if(this.file.type === 'generative'){
        if(name.indexOf(".") != -1){
          name = name.split(".")
          name.pop()
          name = name.join(".")
        }
        name += '<br>(generative)'
      }
      return name
    }
  },
  methods:{
    drag(){
      
    },
    togglePublic(){
      let sendData = {
        user: this.state.loggedinUserName,
        passhash: this.state.loggedinUserHash,
        fileID: this.file.id,
        private: this.file.private==false ? 1 : 0
      }
      fetch(this.state.baseURL + '/setPrivate.php', this.state.fetchObj(sendData))
      .then(json=>json.json()).then(data=>{
        //if(data[0]) this.file.private = !(+this.file.private)
      })
    },
    share(){
      if(this.viewableAsset()){
        window.open(this.state.shareURL + '/' + this.state.decToAlpha(this.file.id), '_blank')
      }
    },
    load(){
      if(this.viewableAsset()){
        if(this.file.type == 'generative'){
          this.state.view(this.state.fileViewerURL + '/generative.php?url=' + this.file.hash)
        } else {
          this.state.view(this.state.fileViewerURL + '/?url=' + this.file.hash)
        }
      } else {
        if(this.file.type == 'folder'){
          window.location.href+=this.file.name+'/'
        } else {
          this.downloadFile()
        }
      }
    },
    renameFile(){
      let newName = prompt('enter a new name', this.file.name)
      if(this.file.name != newName && newName){
        let sendData = {
          user: this.state.loggedinUserName,
          passhash: this.state.loggedinUserHash,
          fileID: this.file.id,
          newName
        }
        fetch(this.state.baseURL + '/renameFile.php', this.state.fetchObj(sendData))
        .then(json=>json.json()).then(data=>{
          console.log('renameFile.php[File.vue]',data)
          if(data[0]) this.file.name = newName
        }) 
      }
    },
    downloadFile(){
      let a = document.createElement('a')
      a.href = '/proxy.php?url=' + this.state.assetsURL + '/' + this.file.hash
      a.download = this.file.name
      a.style.position = 'absolute'
      a.style.visible = 'hidden'
      a.target = "_blank"
      document.body.appendChild(a)
      a.click()
      a.remove()
      
    },
    deleteFile(){
      if(confirm("are you sure?\n\ndeleting this file ("+this.file.name+") cannot be undone!")){
        let sendData = {
          user: this.state.loggedinUserName,
          passhash: this.state.loggedinUserHash,
          fileID: this.file.id
        }
        fetch(this.state.baseURL + '/deleteFile.php', this.state.fetchObj(sendData))
        .then(json=>json.json()).then(data=>{
          if(data[0]){
            this.state.loggedinUserFiles = this.state.loggedinUserFiles.filter(v=>{
              return v.id != this.file.id
            })
          }
        })
      }
    },
    viewableAsset(){
      return this.file.type.indexOf('image') !== -1 ||
             this.file.type.indexOf('video') !== -1 ||
             this.file.type.indexOf('audio') !== -1 ||
             this.file.type.indexOf('generative') !== -1
    }
  },
  mounted(){
    let thumbEl = this.$refs.fileDiv
    let dragHandle = this.$refs.dragHandle
    let containerRect = thumbEl.getBoundingClientRect()
    this.file.fileDiv = this.$refs.fileDiv
    this.file.dragHandle = dragHandle
    this.file.dragHandleRect = dragHandle.getBoundingClientRect()
    this.file.rect = this.$refs.fileDiv.getBoundingClientRect()
 
    dragHandle.onmousedown = e => {
      if(e.button !== 0) return
      e.preventDefault()
      e.stopPropagation()
      this.state.button = true
      let rect = thumbEl.getBoundingClientRect()
      thumbEl.style.position = "absolute"
      this.state.curFileDragging = thumbEl
      this.state.curFileDragging.file = this.file
      this.state.curFileDragging.dropzone = this.dropzone
      this.state.cursorX = rect.x - e.pageX 
      this.state.cursorY = rect.y - e.pageY
    }
    if(this.file.type.indexOf('generative') !== -1){
      thumbEl.style.backgroundRepeat = 'no-repeat'
      thumbEl.style.backgroundPosition = 'center center'
      thumbEl.style.backgroundSize = 'contain'
      thumbEl.style.backgroundImage = 'url(https://jsbot.cantelope.org/uploads/1ALBH1.png)'
    }
    if(this.file.type.indexOf('image')!==-1){
      thumbEl.style.backgroundRepeat = 'no-repeat'
      thumbEl.style.backgroundPosition = 'center center'
      thumbEl.style.backgroundSize = 'contain'
      thumbEl.style.backgroundImage = `url(${this.state.assetsURL + '/' + this.file.hash})`
    }
    if(typeof this.$refs['name_' + this.file.hash] != 'undefined'){
      let fileElement = this.$refs['name_'+this.file.hash]
      //fileElement.style.left = this.file.X + 'px'
      //fileElement.style.top = this.file.Y + 'px'
      fileElement.onmouseover = () => {
        fileElement.style.background = '#0f0'
        fileElement.style.color = '#000';
        fileElement.style.cursor = 'pointer'
      }
      fileElement.onmouseleave = () => {
        fileElement.style.background = '#033'
        fileElement.style.color = '#fff'
        fileElement.style.cursor = 'default'
      }
    }
    if(typeof this.$refs['share_' + this.file.hash] != 'undefined'){
      let fileElement2 = this.$refs['share_'+this.file.hash]
      //fileElement.style.left = this.file.X + 'px'
      //fileElement.style.top = this.file.Y + 'px'
      fileElement2.onmouseover = () => {
        fileElement2.style.background = '#0f0'
        fileElement2.style.color = '#000';
        fileElement2.style.cursor = 'pointer'
      }
      fileElement2.onmouseleave = () => {
        fileElement2.style.background = '#033'
        fileElement2.style.color = '#fff'
        fileElement2.style.cursor = 'default'
      }
    }
    if(this.state.loggedinUserFiles.length == 1) this.load()
  }
}

</script>

<style scoped>
  .basicIcon{
    background-repeat: no-repeat!important;
    background-position: center center!important;
    background-size: 50px!important;
    background-image: url(https://jsbot.cantelope.org/uploads/2bceZU.png)!important;
  }
  .notBasicIcons{
  }
  .fileButton{
    width: 20px;
    height: 20px;
    border: none;
    cursor: pointer;
    margin: 2px;
    display: inline-block;
    border-radius: 5px;
    background-position: center center;
    background-size: 16px 16px;
    background-repeat: no-repeat;
  }
  .dragHandle{
    width: 125px;
    height: 100px;
    margin-top: -20px;
    background: #f000;
    position: absolute;
    z-index: 10;
  }
  .dragHandle:hover{
    background: #0f00;
  }
  .privateCheckbox{
  }
  .renameButton{
    background-color: #8fca;
    background-image: url(https://jsbot.cantelope.org/uploads/11tQv3.png);
  }
  .downloadButton{
    background-color: #086;
    background-image: url(https://jsbot.cantelope.org/uploads/2c0FSr.png);
  }
  .deleteButton{
    background-color: #200;
    background-image: url(https://jsbot.cantelope.org/uploads/XeGsK.png);
  }
  .fileName{
    background: #033;
    padding: 5px;
    margin: 5px;
    margin-top: 10px;
    position: relative;
    z-index: 100;
    border-radius: 2px;
    white-space: break-spaces;
    word-break: break-all;
    max-width: 85px;
    min-width: 85px;
    padding-left: 10px;
    padding-right: 10px;
    line-height: 1;
  }
  .shareLink{
    padding: 5px;
    display: inline-block;
    margin: 0px;
    margin-bottom: 5px;
    min-width: 95px;
    vertical-align: top;
    font-size: 10px;
    color: #fff;
    text-decoration: none;
  }
  .thumb{
    width: 100%;
    height: 100%;
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
  }
  .fileButtons{
    text-align: center;;
    margin-top: -15px;
    position: absolute;
    margin-left: 10px;
    z-index: 100;
  }
  .warning{
    background: #400!important;
  }
  .file{
    padding: 0px;
    padding-top: 6px;
    display: inline-block;
    margin: 5px;
    min-width:70px;
    vertical-align: top;
    display: inline-block;
    font-size: 10px;
    color: #fff;
  }
</style>
