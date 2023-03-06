<template>
  <div
    class="dropzone"
    :class="{
      'dragOver': draggingOver,
      'fileUploading': showProgress,
      'finished': finished
    }"
    ref="drop_zone"
    @drop="dropHandler"
    @dragover="dragOverHandler"
    @dragleave="dragLeaveHandler"
    @mouseup="mouseupHandler"
  >
    <DZTools v-if="0" :state="state" :caption="caption"/>
    <div v-if="!showProgress && !finished">
      <div v-for="(file, idx) in state.loggedinUserFiles" :key="idx" class="fileContainer">
        <File :state="state" :file="file" :dropzone="dropzone"/>
      </div>
    </div>
    <div v-else>
      <div v-if="finished">
        <div class="fileContainer" :key="idx" v-for="file in state.loggedinUserFiles" >
          <File :state="state" :file="file" />
        </div>
      </div>
      <div v-else>
        uploading...
        <div v-if="1" v-for="(file, idx) in fileList" :key="idx">
          <span class="fileName" v-html="fileNameFormatted(file.name)"></span>
          <div class="progressBar" :key="'outer_'+idx">
            <div :style="`width:calc(${file.perc}%)`" class="innerProgress" :key="'inner_'+idx"></div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="0" v-for="i in 10">
      <div class="progressBar" :key="'outer_'+idx">
        <div :style="`width:calc(${i*10}%)`" class="innerProgress" :key="'inner_'+idx"></div>
      </div>
      {{JSON.stringify(this.fileList)}}
    </div>
  </div>
</template>

<script>
import File from './File'
import DZTools from './DZTools'

export default {
  name: 'Dropzone',
  components: {
    File,
    DZTools,
  },
  props: ['state', 'caption'],
  data(){
    return {
      testData : "it works!",
      draggingOver: false,
      showProgress: false,
      fileList: [],
      count: 0,
      finished: false,
      drag: false,
      showUploadProgress: false
    }
  },
  computed:{
    dropzone(){
      return this.$refs['drop_zone']
    },
    filteredFiles(){
      let rw = 100
      let cl = 5
      let hsp = 120
      let vsp = 100
      return this.state.loggedinUserFiles.map((v,i)=>{
        v.X = 20 + (i%cl)*hsp
        v.Y = 175 + ((i/cl|0)%rw)*vsp
        return v
      })
    }
  },
  methods:{
    setWidth(idx){
      return `width: calc(${this.fileList[idx].perc}%)`
    },
    fileNameFormatted(name){
      return name.length <= 30 ? name:name.substring(0, 20) + '...' + name.substring(name.length-6)
    },
    dropHandler(e){
      let error = false
      e.preventDefault()
      if(this.showProgress) return
      //e.stopPropagation()
      this.count = 0
      let files
      if (e.dataTransfer.items) {
        this.fileList = [];
        this.count=[...e.dataTransfer.items].length;
        [...e.dataTransfer.items].forEach((item, i) => {
          if (item.kind === 'file') {
            const file = item.getAsFile()
            this.showProgress = true
            this.draggingOver = false
            this.fileList = [...this.fileList, file]

            this.fileList.map((v,i)=>{
              v.completed = false
              v.perc = 0
              v.idx = i
            })
          }
        })
        this.fileList.map((v, i)=>{
          if(error) return
          if(
            //(v.type == 'audio/mpeg' ||
            //v.type == 'audio/ogg' ||
            //v.type == 'audio/wav') &&
            v.size < 100000000
          ){
            let data = new FormData()
            data.append('user', this.state.loggedinUserName)
            data.append('passhash', this.state.loggedinUserHash)
            data.append('name', v.name)
            data.append('userID', this.state.loggedinUserID)
            data.append('location', this.state.loggedinUserLocation)
            data.append('description', '')
            data.append('file', v)
            let request = new XMLHttpRequest()
            request.open('POST', this.state.baseURL + '/upload.php')
            let tidx = i
            request.upload.addEventListener('progress', e => {
              let perc = (e.loaded / e.total)*100
              this.fileList[tidx].perc = perc
            })
            request.onreadystatechange = e => {
              if(e.status ==200 && e.readyState == 4){
              }
            }
            request.addEventListener('load', e=>{
              v.completed = true
              let finished = true
              this.fileList.map(q=>{
                if(!q.completed) finished = false
              })
              if(finished) {
                this.state.loadLoggedInUserData()
                this.finished = true
                this.draggingOver = false
                this.showProgress = false
              }
            })
            request.send(data)
          } else {
            this.showProgress = false
            this.finished = false
            this.draggingOver = false
            error = true
            alert('a file was rejected due to incorrect type or filesize (max filesize = 100MB)')
          }
        })
      }else{
        this.fileList = [];
        this.count=[...e.dataTransfer.files].length;
        [...e.dataTransfer.files].forEach((file, i) => {
          if(error) return
          this.showProgress = true
          this.draggingOver = false
          this.fileList = [...this.fileList, file]


          let files = this.fileList
          Array.from(files).forEach((v, i)=>{
            v.completed = false
            this.fileList[i].idx = i
          })
          Array.from(files).forEach((v, i)=>{
            if(
              //(v.type == 'audio/mpeg' ||
              //v.type == 'audio/ogg' ||
              //v.type == 'audio/wav') &&
              v.size < 100000000
            ){
              let data = new FormData()
              data.append('user', this.state.loggedinUserName)
              data.append('passhash', this.state.loggedinUserHash)
              data.append('name', v.name)
              data.append('userID', this.state.loggedinUserID)
              data.append('location', this.state.loggedinUserLocation)
              data.append('description', '')
              data.append('file', v)
              let request = new XMLHttpRequest()
              request.open('POST', this.state.baseURL + '/upload.php')
              let tidx = i
              request.upload.addEventListener('progress', e => {
                let perc = (e.loaded / e.total)*100
                this.fileList[tidx].perc = perc
              })
              request.onreadystatechange = e => {
                if(e.status ==200 && e.readyState == 4){
                }
              }
              request.addEventListener('load', e=>{
                v.completed = true
                let finished = true
                Array.from(files).forEach(q=>{
                  if(!q.completed) finished = false
                })
                if(finished) {
                  //window.location.href = window.location.origin + '/u/' + this.state.loggedinUserName
                  this.finished = true
                  this.draggingOver = false
                  this.showProgress = false
                }
              })
              request.send(data)
            } else {
              this.draggingOver = false
              this.showProgress = false
              error = true
              alert('a file was rejected due to incorrect type or filesize (max filesize = 100MB)')
            }
          })

        })
      }
    },
    dragLeaveHandler(e){
      this.draggingOver = false
      e.preventDefault()
      e.stopPropagation()
    },
    mouseUpHandler(e){
      this.draggingOver = false
      e.preventDefault()
      e.stopPropagation()
    },
    dragOverHandler(e){
      e.preventDefault()
      e.stopPropagation()
      if(this.showProgress) return
      if(!this.showProgress) this.finished=false
      this.draggingOver = true
    }
  },
  mounted(){
    window.onmousemove=()=>{
      this.draggingOver = false
    }
  }
}

</script>

<style scoped>
  .dropzone{
    padding: 20px;
    padding-top: 5px;
    color: #fff;
    background: #333;
    display: inline-block;
    margin: 0;
    width: calc(100vw - 40px);
    position: absolute;
    top: 71px;
    height: calc(100vh - 98px);
    border: 1px solid #4f84;
    vertical-align: top;
    overflow-x: hidden;
    overflow-y: auto;
    text-align: center;
  }
  .fileContainer{
    top: 100px;
    align-content: flex-start;
    display: inline-flex;
  }
  .fileUploading{
    background: #4f88!important;
  }
  .progressBar{
    width: 100%;
    border: 1px solid #fff6;
    height: 10px;
    margin-bottom: 10px;
  }
  .innerProgress{
    background: #8ff;
    height: 100%;
  }
  .finished{
    background: #40f2!important;
    color: #f44;
  }
  .fileName{
    color: #8f8; 
  }
  .dragOver{
    background: #0ff1!important;
  }
  .desc{
    position: absolute;
    top: 0;
    margin-top: 5px;
    margin-left: 125px;
  }
</style>
