<script setup lang="ts">
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import { onBeforeMount, ref } from "vue";

const props = defineProps(["mode"]);

const _toolbar = [
    ["bold", "italic", "underline", "strike"], // toggled buttons
    ["blockquote", "code-block"],
    ["link", "image"],
    [],
    [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
    [{ script: "sub" }, { script: "super" }],
    [{ indent: "-1" }, { indent: "+1" }],
    [{ direction: "rtl" }],
    ["clean"],
];
const content = defineModel();
const options = ref({
    modules: {
        toolbar: _toolbar,
    },
    placeholder: "Compose an exciting content here...",
    readOnly: props.mode === "read",
    theme: "snow",
});
const myQuillEditor = ref(null);

onBeforeMount(() => {
    if (props.mode === "read") {
        options.value.modules.toolbar = false;
    }
});
const onReady = () => {
    if (props.mode === "read") {
        options.value.modules.toolbar = false;
        myQuillEditor.value.getEditor().style.border = "none";
        myQuillEditor.value.getEditor().firstElementChild.style.paddingTop =
            "0";
        myQuillEditor.value.getEditor().firstElementChild.style.paddingBottom =
            "0";
        myQuillEditor.value.getEditor().firstElementChild.style.fontSize =
            "1rem";
    }
};
</script>

<template>
    <QuillEditor
        ref="myQuillEditor"
        :options="options"
        @ready="onReady"
        v-model:content="content"
    >
    </QuillEditor>
</template>

<style>
.ql-container {
    min-height: 15rem;
    height: 100%;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.ql-editor {
    font-family: "Inter", sans-serif;
    height: 100%;
    flex: 1;
    overflow-y: auto;
    width: 100%;
    font-size: 0.9rem;
    word-break: break-all;
}
</style>
