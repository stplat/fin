<template>
  <div class="VueTables__search-field">
    <input class="VueTables__search__input"
           type="text"
           placeholder="Search..."
           @keyup="e=>props.search(props.debounce)(e)"
           :id="`VueTables__search_${props.id}`"
           autocomplete="off"
    />
  </div>
</template>

<script>
  export default {
    name: "VtGenericFilter",
    props: ['props']
  }
</script>
