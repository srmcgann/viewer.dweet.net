<template>
  <div class="DZTools">
    <div class="caption" v-html="caption"></div><br>
    <div
      ref="parentFolderDropTarget" 
      @click="goUp()"
      :title="'click to go up a directory'"
      v-if="this.state.loggedinUserLocation !== '/'"
      class="folderIcon fileDiv parentFolder"
    >
      <div class="specialIcon">
        drop items here to<br>
        move to parent directory
      </div>
    </div>
    <div class="descText">Drag one or more files to this <i>drop zone</i>.<br>
      <span style="float: left;">tools:</span><br>
      <button
      title="create new folder"
      @click="createFolder()"
      class="DZToolsButton folderButton"
      >create<br>folder</button>
    </div>
  </div>
</template>

<script>

export default {
  name: 'DZTools',
  props: ['state', 'caption'],
  data(){
    return {
      testData : "it works!"
    }
  },
  methods:{
    goUp(){
      let l = window.location.href.split('/')
      l.pop();l.pop()
      l=l.join('/');window.location.href=l+'/'
    },
    createFolder(){
      let folderName = prompt('enter the name of the folder:', 'new folder name');
      if(!folderName) return
      let sendData = {
        user: this.state.loggedinUserName,
        passhash: this.state.loggedinUserHash,
        currentLocation: this.state.loggedinUserLocation,
        folderName: folderName
      }
      fetch(this.state.baseURL + '/createFolder.php', this.state.fetchObj(sendData))
      .then(json=>json.json()).then(data=>{
        if(data[0]){
          this.state.loggedinUserFiles.push(data[2])
          this.state.positionFilesAbsolutely(true) 
        }else{
          console.log('error creating folder!')
        }
      })
    }
  },
  mounted(){
    this.state.parentFolderDropTarget = this.$refs.parentFolderDropTarget
  }
}

</script>

<style scoped>
  .DZTools{
    padding: 5px;
    color: #fff;
    background:#0003;
    margin: 0;
    text-align: left;
    top:0;
    min-height: 95px;
    left: 0;
    max-width: calc(100% - 10px);
    margin-top: 5px;
    width: 100vw;
  }
  .parentFolder{
   }
  .parentFolder{
    width: 60px;
    height: 60px;
    cursor: pointer;
    min-height: unset;
    padding: unset;
    margin: 10px;
    position: absolute;
    left: 30px;
    margin-top: 10px;
  }
  .folderButton{
    background-image: url(https://jsbot.cantelope.org/uploads/2jP7OJ.png);
  }
  .caption{
    top: 5px;
    font-size: 1.3em;
    float: left;
    color: #afa;
  }
  .descText{
    margin-left: 120px;
    margin-top: -20px; 
  }
  .DZToolsButton{
    background-color: #4fc6;
    background-position: center center;
    background-repeat: no-repeat;
    background-size: 30px;
    border-radius: 10px;
    padding: 0px;
    color: #fff;
    border: 1px solid #4f88;
    text-shadow: 2px 2px 2px #000;
    margin: 5px;
    height: 35px;
    cursor: pointer;
    min-width: 50px;
    display: inline-block;
  }
  .specialIcon{
    position: absolute;
    width: 65px;
    line-height: 1em;
    margin-top: -2px;
    font-size: 10px;
    padding: 8px;
    background: #0004;
    color: #fff;
    margin-left: -12px;
    text-shadow: 1px 1px 2px #000;
    border-radius: 5px;
  }
</style>
