<script setup>
	import Pagination from "@/Shared/Pagination.vue";
	import RowGroup from "@/Shared/RowGroup.vue";
	import {ref, watch} from "vue";
	import {router} from "@inertiajs/vue3";

	let props = defineProps({
		clients: Object,
		client: Object,
		filters: Object,
		errors: Object
	});
	
	let activeClientRowDataKey = ref('');
	let activeCarRowDataKey = ref('');
	let cars = ref([]);
	let services = ref([]);
	
	const getCars = async (client_id) => {
		if(activeClientRowDataKey.value !== client_id) {
			await axios.get( "clients/" + client_id + "/cars" ).then( ( response ) => {
				cars.value = response.data;
				activeClientRowDataKey.value = client_id;
				activeCarRowDataKey.value = '';
			}).catch( ( error ) => {
				console.log( 'Autók lekérése sikertelen', error );
			});
		} else {
			activeClientRowDataKey.value = '';
			activeCarRowDataKey.value = '';
			cars.value = [];
			services.value = [];
		}
	}
	
	const getServices = async (car_id) => {
		if(activeCarRowDataKey.value !== car_id) {
			await axios.get( "clients/" + activeClientRowDataKey.value + "/cars/" + car_id + "/services").then( ( response ) => {
				services.value = response.data;
				activeCarRowDataKey.value = car_id;
			}).catch( ( error ) => {
				console.log( 'Szervíznaplók lekérése sikertelen', error );
			});
		} else {
			activeCarRowDataKey.value = '';
			services.value = [];
		}
	}
	
	let errorMessages = ref(props.errors);
	
	watch(() => props.errors, (newErrors) => {
		errorMessages.value = newErrors;
	});
	
	let searchByName = ref(props.filters.searchByName);
	let searchByCardNumber = ref(props.filters.searchByCardNumber);
	
	const submitSearch = async () => {
		if(searchByName.value && searchByCardNumber.value) {
			errorMessages.value = { "search_by_name": "Csak az egyik mezőt töltse ki!"};
			return;
		}
		
		await router.get("/", { search_by_name: searchByName.value, search_by_card_number: searchByCardNumber.value}, {
			preserveState: true,
			replace: true,
		});
	};
	
</script>

<template>
	<h1 class="text-2xl">Ügyfelek</h1>
	
	<p>activeClientRowDataKey: {{ activeClientRowDataKey }}</p>
	<p>activeCarRowDataKey: {{ activeCarRowDataKey }}</p>
	
	<div class="my-3 p-4 bg-white rounded border border-[#c7c7c7] shadow">
		<div class="flex ">
			
			<div class="items-center">
				<label class="uppercase me-1 font-bold text-xs text-gray-700">Ügyfél:</label>
				<input
					type="text"
					v-model="searchByName"
					class="border p-2 rounded-lg"
				>
				<br>
				<div v-if="errorMessages.search_by_name" v-text="errorMessages.search_by_name"
				     class="text-red-500 text-xs mt-1"></div>
			</div>
			
			<div class="items-center">
				<label class="uppercase ms-2 me-1 font-bold text-xs text-gray-700">Okmányazonosító:</label>
				<input
					type="text"
					v-model="searchByCardNumber"
					class="border p-2 rounded-lg"
				>
			</div>
			
			<div class="items-center">
				<button @click="submitSearch" class="ms-3 bg-blue-700 text-white rounded p-1.5 hover:opacity-90">Keresés
				</button>
			</div>
		</div>
		
		<div v-if="client" class="gap-x-2">
			<p>Azonosító: {{ client.id}}</p>
			<p>Név: {{ client.name}}</p>
			<p>Okmányazonosító: {{ client.card_number}}</p>
			<p>Autóinak száma: {{ client.cars_count}}</p>
			<p>Autók szerviznaplóbejegyzéseinek száma: {{ client.services_count}}</p>
		</div>
	</div>
	
	<div class="flex items-center p-4 bg-white rounded-lg justify-between">
		<div>
			<p class="mb-2">Találatok száma: {{ clients.from }} - {{ clients.to }} / {{ clients.total }}</p>
			
			<Pagination :links="clients.links" />
		</div>
	</div>
	
	<div class="flex flex-col my-4 shadow">
		<div class="-m-1.5 overflow-x-auto">
			<div class="p-1.5 min-w-full inline-block align-middle">
				<div class="border border-[#c7c7c7] rounded-lg shadow overflow-hidden">
					<table class="min-w-full divide-y divide-[#c7c7c7]">
						<thead class="bg-[#eaeaea]">
							<tr class="divide-x divide-[#c7c7c7] text-center">
								<th scope="col" class="p-3 text-xs font-medium text-[#777] uppercase">Ügyfél azonosító</th>
								<th scope="col" class="p-3 text-xs font-medium text-[#777] uppercase">Név</th>
								<th scope="col" class="p-3 text-xs font-medium text-[#777] uppercase">Okmányazonosító</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-[#c7c7c7]">
							<RowGroup v-for="client in clients.data" :key="client.id" >
								<tr class="divide-x divide-[#c7c7c7] bg-white hover:bg-gray-100 hover:bg-opacity-90">
									<td class="p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center">{{ client.id }}</td>
									<td class="p-3 whitespace-nowrap text-sm font-medium text-blue-600 hover:underline cursor-pointer"><span @click="getCars(client.id)">{{ client.name }}</span></td>
									<td class="p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center">{{ client.card_number }}</td>
								</tr>
								<tr
									v-if="activeClientRowDataKey === client.id"
									class="divide-x divide-[#c7c7c7] bg-white"
								>
									<td colspan="10" class='p-4'>
										<div class="flex flex-col my-4">
											<div class="-m-1.5 overflow-x-auto">
												<div class="p-1.5 min-w-full inline-block align-middle">
													<div class="border border-[#c7c7c7] rounded-lg shadow overflow-hidden">
														<table class='min-w-full divide-y divide-[#c7c7c7]'>
															<thead class='bg-[#eaeaea]'>
																<tr class='divide-x divide-[#c7c7c7] text-center'>
																	<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Autó sorszáma</th>
																	<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Autó típusa</th>
																	<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Regisztrálás időpontja</th>
																	<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Saját márkás-e</th>
																	<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Balesetek száma</th>
																	<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Utolsó esemény</th>
																	<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Utolsó esemény időpontja</th>
																</tr>
															</thead>
															<tbody class='divide-y divide-[#c7c7c7]'>
																<tr v-if="cars.length === 0" class="bg-white">
																	<td colspan="10" class='p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center'>Nincs megjeleníthető eredmény</td>
																</tr>
																<RowGroup v-for="car in cars" :key="car.id" >
																	<tr class="divide-x divide-[#c7c7c7] bg-white hover:bg-gray-100 hover:bg-opacity-90">
																		<td class='p-3 whitespace-nowrap text-sm font-medium text-blue-600 text-center hover:underline cursor-pointer'><span @click='getServices(car.id)'>#{{ car.car_id }}</span></td>
																		<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800'>{{ car.type }}</td>
																		<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center'>{{ car.registered }}</td>
																		<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center'>{{ car.ownbrand ? "Igen" : "Nem" }}</td>
																		<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center'>{{ car.accidents }}</td>
																		<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800'>{{ car.services[0].event }}</td>
																		<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center'>{{ car.services[0].event_time }}</td>
																	</tr>
																	<tr
																		v-if="activeCarRowDataKey === car.id"
																		class="divide-x divide-[#c7c7c7] bg-white"
																	>
																		<td colspan="10" class='p-4'>
																			<div class="flex flex-col my-4">
																				<div class="-m-1.5 overflow-x-auto">
																					<div class="p-1.5 min-w-full inline-block align-middle">
																						<div class="border border-[#c7c7c7] rounded-lg shadow overflow-hidden">
																							<table class='min-w-full divide-y divide-[#c7c7c7]'>
																								<thead class='bg-[#eaeaea]'>
																								<tr class='divide-x divide-[#c7c7c7] text-center'>
																									<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Alkalom sorszáma</th>
																									<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Esemény</th>
																									<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Esemény időpontja</th>
																									<th scope='col' class='p-3 text-xs font-medium text-[#777] uppercase'>Munkalap azonosító</th>
																								</tr>
																								</thead>
																								<tbody class='divide-y divide-[#c7c7c7]'>
																								<tr v-if="services.length === 0" class="bg-white">
																									<td colspan="10" class='p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center'>Nincs megjeleníthető eredmény</td>
																								</tr>
																								<RowGroup v-for="service in services" :key="service.id" >
																									<tr class="divide-x divide-[#c7c7c7] bg-white hover:bg-gray-100 hover:bg-opacity-90">
																										<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center'>#{{ service.log_number }}</td>
																										<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800'>{{ service.event }}</td>
																										<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center'>{{ service.event_time ?? car.registered }}</td>
																										<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800 text-center'>{{ service.document_id }}</td>
																									</tr>
																								</RowGroup>
																								</tbody>
																							</table>
																						</div>
																					</div>
																				</div>
																			</div>
																		</td>
																	</tr>
																</RowGroup>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
							</RowGroup>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<div class="flex items-center p-4 bg-white rounded-lg justify-between">
		<div>
			<p class="mb-2">Találatok száma: {{ clients.total }}</p>
			
			<Pagination :links="clients.links" />
		</div>
	</div>
	
</template>

<style scoped>

</style>