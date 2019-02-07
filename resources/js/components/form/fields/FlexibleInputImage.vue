<template>
  <div class="flexible-input-container input-file">
    <div class="input-file__input-field">
      <label class="input-label" :for="id">
        {{ data.label }}
        <span class="input-label__required" v-if="data.required === true">*</span>
      </label>
      <p>Click here to upload</p>
      <input
        :id="id"
        type="file"
        class="input-file__input"
        :multiple="data.multiple ? true : false"
        :required="data.required === true && !data.value"
        v-on:change="handleChange($event.target.files)"
      >
    </div>
    <div class="input-file__values uploading" v-if="images.length > 0">
      <div class="input-file__single" v-if="!data.multiple">
        <img :src="images[0].src">
      </div>
      <div class="input-file__gallery" v-if="data.multiple">
        <div class="input-file__gallery__item" v-for="(image, index) in images" :key="index">
          <img :src="image.src">
          <div class="input-file__gallery__item__actions">
            <button class="btn-order-up" v-on:click.prevent="handleDirection(index, 'up')">Left</button>
            <button class="btn-delete" v-on:click.prevent="handleDeleteImage(index)">Remove</button>
            <button class="btn-order-down" v-on:click.prevent="handleDirection(index, 'down')">Right</button>
          </div>
        </div>
      </div>
      <button class="btn btn-default btn-primary" v-on:click.prevent="uploadImages()">Upload</button>
    </div>

    <div class="input-file__values" v-if="data.value">
      <div class="input-file__single" v-if="!data.multiple">
        <img :src="data.value">
      </div>
      <div class="input-file__gallery" v-if="data.multiple">
        <div class="input-file__gallery__item" v-for="(image, index) in data.value" :key="index">
          <img :src="image">
          <div class="input-file__gallery__item__actions">
            <button
              class="btn-order-up"
              v-on:click.prevent="handleDirection(index, 'up', true)"
            >Left</button>
            <button class="btn-delete" v-on:click.prevent="handleDeleteImage(index, true)">Remove</button>
            <button
              class="btn-order-down"
              v-on:click.prevent="handleDirection(index, 'down', true)"
            >Right</button>
          </div>
        </div>
      </div>
      <button
        class="btn btn-default btn-danger"
        v-on:click.prevent="removeImage()"
      >{{ data.multiple ? 'Remove all' : 'Remove' }}</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "flexible-input-image",
  props: ["id", "data", "onChange"],
  data() {
    return {
      images: [],
      loading: false,
      files: null
    };
  },
  methods: {
    removeImage() {
      this.files = [];
      this.images = [];
      this.onChange(null);
    },
    handleDirection(index, dir, isValue = false) {
      const img = isValue ? this.data.value[index] : this.images[index];

      const images = isValue ? this.data.value : this.images;
      if (dir == "up") {
        const up = images[index - 1];
        if (up) {
          images[index - 1] = img;
          images[index] = up;
        }
      } else {
        const down = images[index + 1];
        if (down) {
          images[index + 1] = img;
          images[index] = down;
        }
      }
      if (isValue) {
        this.onChange(images);
      } else {
        this.images = [...images];
      }
    },
    handleDeleteImage(index, isValue = false) {
      if (isValue) {
        this.onChange(this.data.value.filter((__, i) => i !== index));
      } else {
        this.images = this.images.filter((__, i) => i !== index);
      }
    },
    uploadImages() {
      const { value: oldValue, multiple = false } = this.data;
      const files = this.files || [];
      const form = new FormData();

      this.images.forEach(image => {
        form.append("files[]", files[image.file]);
      });

      this.loading = true;

      axios
        .post("/nova-vendor/flexible-content/upload-files", form)
        .then(({ data = {} }) => {
          this.loading = false;

          if (data && data.status && data.fileurls) {
            this.files = [];
            this.images = [];

            document.getElementById(this.id).value = "";
            if (oldValue && !multiple) {
              axios.post("/nova-vendor/flexible-content/delete-files", {
                fileurls: typeof oldValue === "string" ? [oldValue] : oldValue
              });
            }
            this.onChange(
              multiple
                ? oldValue
                  ? [...oldValue, ...data.fileurls]
                  : data.fileurls
                : data.fileurls[0]
            );
          }
        });
    },
    handleChange(files) {
      const { multiple = false } = this.data;
      const save = this.onChange;
      this.files = files;
      if (files && files.length > 0) {
        if (multiple) {
          this.images = [];
          for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();

            reader.onload = e => {
              const img = e.target.result;
              this.images = [...this.images, { src: img, file: i }];
            };

            // read the image file as a data URL.
            reader.readAsDataURL(files[i]);
          }
        } else {
          this.images = [];
          const reader = new FileReader();

          reader.onload = e => {
            const img = e.target.result;
            this.images = [{ src: img, file: 0 }];
          };

          // read the image file as a data URL.
          reader.readAsDataURL(files[0]);
        }
      }
    }
  }
};
</script>

<style lang="scss">
.input-file {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  align-items: flex-start;

  &__input-field {
    padding: 10px;
    margin: 10px 15px 0 0;
    text-align: center;
    background: #cccccc;
    border-radius: 50%;
    height: 120px;
    width: 120px;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
    align-self: flex-start;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    label {
      display: block;
      margin: 0 0 7px;
      background: none !important;
    }
  }
  &__input {
    margin: 10px 0;
    cursor: pointer;
    opacity: 0;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    z-index: 10;
  }
  &__values {
    flex-grow: 1;
    padding: 15px;
    min-width: 50%;

    img {
      max-width: 100%;
      max-height: 150px;
    }
    &.uploading {
      background: #eee;

      img {
        max-height: 100px;
      }
    }
  }
  &__gallery {
    display: flex;
    flex-grow: 1;
    flex-direction: row;
    justify-content: flex-start;
    align-items: stretch;
    flex-wrap: wrap;

    &__item {
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      align-items: flex-start;
      width: 25%;
      margin: 5px;
      background: #ccc;

      &__actions {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        background: #313131;
        padding: 5px;
        text-align: right;
        margin: auto 0 0 0;
        width: 100%;

        button {
          font-size: 11px;
          line-height: 16px;
          color: #ffffff;
          padding: 0;
          margin: 0 5px;
        }
        .btn-delete {
          color: #e74444;
          font-weight: bold;
        }
      }

      &:first-child {
        .btn-order-up {
          opacity: 0;
        }
      }
      &:last-child {
        .btn-order-down {
          opacity: 0;
        }
      }
    }
  }
}
</style>
