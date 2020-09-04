<template>
  <popup @close="$emit('close')" :isLoading="isLoading">
    <template v-slot:header>Импорт бюджетных параметров</template>
    <template v-slot:body>
      <div class="row">
        <div class="col-md-12">
          <alert :className="'info'">Ранее загруженные данные по версии - <strong>{{ version }}</strong> будут удалены!
          </alert>
          <alert v-for="(error, i) in errors" :key="i" v-html="error"/>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="file" class="text-muted"><strong>Файл для импорта:</strong></label>
            <div class="form-content">
              <p>Загрузите файл с компьютера. Ограничения:
              <p>- формат файла: <strong>xls, xlsx</strong>
              <p>- первая строка: <strong>Заголовки</strong>
              <p>- <a :href="assetBase + 'storage/budget_layout.xlsx'" download>образец файла</a></p>
            </div>
            <div class="form-group-file mt-4">
              <div class="form-group-file__name mr-3"><p>{{ data.upload.name ? data.upload.name :
                'Ни одного файла не выбрано' }}</p></div>
              <label for="file" class="btn btn-secondary">Выбрать файл</label>
              <input hidden type="file" id="file" ref="file" @change="changeFile">
            </div>
          </div>
        </div>
      </div>
    </template>
    <template v-slot:footer>
      <button class="btn btn-primary text-white ml-auto" @click="upload">Загрузить</button>
    </template>
  </popup>
</template>

<script>
  export default {
    data() {
      return {
        data: {
          version: '',
          upload: {
            name: '',
            file: ''
          },
          periods: [],
          version_involvement: null,
          regions: null
        },
        show: false,
        isLoading: false,
        errors: []
      }
    },
    props: {
      initialData: {
        type: Object,
        required: true
      },
      versions: {
        type: Array,
        required: true
      }
    },
    mounted() {
      this.data.period = this.initialData.periods[0];
      this.data.version = this.initialData.version;
      this.data.periods = this.initialData.periods;
      this.data.regions = this.initialData.regions;
      this.data.version_involvement = this.initialData.version_involvement;
    },
    computed: {
      period() {
        return this.periods.filter(item => item.id === this.data.period)[0].name;
      },
      version() {
        return this.versions.filter(item => item.id === this.data.version)[0].name;
      }
    },
    methods: {
      /* Валидация входных данных */
      validate(file) {
        this.errors = [];

        const allowFormat = [
          'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
          'application/excel',
          'application/vnd.ms-excel',
          'application/vnd.msexcel',
        ].findIndex(item => item === file.type);

        file === '' ? this.errors.push('Поле <strong>Файл для импорта</strong> обязательно для заполнения') :
          allowFormat !== -1 ? '' : this.errors.push('Допустимый формат файлов <strong>xls, xlsx</strong>');

        return !this.errors.length;
      },

      /* Событие изменения (загрузки) файла */
      changeFile(e) {
        const file = e.target.files[0];
        this.data.upload.name = file.name;
        this.data.upload.file = file;
      },

      /* Загрузка перевозчика */
      upload() {
        let { upload, version, regions, periods, version_involvement } = this.data;

        if (this.validate(upload.file)) {
          this.isLoading = true;

          this.$store.dispatch('budget/uploadBudget', { file: upload.file, version, regions, periods, version_involvement })
            .then(res => {
              this.errors = res.errors;
              this.isLoading = false;

              if (!res.hasOwnProperty('errors')) {
                this.errors = [];
                this.$emit('setResult', `Файл <strong>${upload.name}</strong> успешно загружен!`);
                this.$emit('close');
              }
            });
        }
      }
    },
  }


</script>
