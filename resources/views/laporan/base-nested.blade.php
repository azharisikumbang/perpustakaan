<table>
	<thead>
		<tr></tr>
		<tr>
			<th 
			style="height: 30px; text-align: left; vertical-align: center; font-weight: bold; font-size: 18px"
			colspan="{{ count($handler->getHeader()) + 1 }}">{{ strtoupper($handler->getTitle()) }}</th>
		</tr>
		<tr>
			<th colspan="2">Dibuat tanggal : {{ $handler->getTimestamp()->format('d/m/Y H:i:s') }}</th>
		</tr>
		<tr></tr>
		<tr style="">
			<th style="border: 1px solid #000; font-weight: bold; text-align: center; vertical-align: center; background-color: yellow">No</th>
			@foreach($handler->getHeader() as $header)
			<th style="border: 1px solid #000; font-weight: bold; text-align: center; vertical-align: center; background-color: yellow">{{ $header }}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($handler->getData() as $data)
			@for ($i = 0; $i < $data['rows']; $i++)
			<tr>
				@if($i < 1)
				<td style="border: 1px solid #000; vertical-align: center; text-align: center" rowspan="{{ $data['rows'] }}">{{ $loop->iteration }}</td>
				@endif
				@foreach($handler->getHeader() as $key => $header)
					@if(is_array($data[$key]))
						<td style="border: 1px solid #000; vertical-align: center">{{ $data[$key][$i] ?? '-' }}</td>
					@else
						@if($i < 1)
							<td style="border: 1px solid #000; vertical-align: center" rowspan="{{ $data['rows'] }}">{{ $data[$key] ?? '-' }}</td>
						@endif
					@endif
				@endforeach
			@endfor
		@endforeach
	</tbody>
</table>