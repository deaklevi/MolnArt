<template>

<div class="max-w-4xl mx-auto p-6 font-sans">
    <ClientOnly>
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
          <h1 class="text-3xl font-black text-slate-900 tracking-tight">Beosztás</h1>
          <p class="text-slate-500 font-medium">Szerkezd beosztásod tetszés szerint</p>
        </div>
        <NuxtLink to="/dashboard" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all shadow-sm">
          ← Irányítópult
        </NuxtLink>
      </div>
    </ClientOnly>
</div>

</template>
<script setup>

</script>