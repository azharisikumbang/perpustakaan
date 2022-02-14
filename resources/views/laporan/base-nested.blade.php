<table>
	<thead><?php $cols = count($handler->getHeader()) + 1; ?>
		<tr>
			<th style="text-align: center; vertical-align: bottom; height: 50px" colspan="{{ $cols }}">
				KEMENTERIAN AGAMA REPUBLIK INDONESIA
				<p>BALAI PENDIDIKAN DAN PELATIHAN KEAGAMAAN PADANG</p>
			</th>
		</tr>
		<tr>
			<th style="text-align: center; vertical-align: top; height: 45px; border-bottom: 5px solid #000;" colspan="{{ $cols }}">
				Jalan Batang kapur no. 7 padang 25138 telepon. (0751) 7053807 faksimili (0751) 411 69
				<p>Website :http://bdkpadang.kemenag.go.id Email : bdkpadang@kemenag.go.id</p>
			</th>
		</tr>
		<tr></tr>
		<tr>
			<th 
			style="height: 16px; text-align: left; vertical-align: center; font-weight: bold; font-size: 14px"
			colspan="{{ $cols }}">{{ strtoupper($handler->getTitle()) }}</th>
		</tr>
		<tr>
			<th colspan="{{ $cols }}">Dibuat tanggal : {{ $handler->getTimestamp()->format('d/m/Y H:i:s') }} WIB</th>
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
	<tfoot>
		<tr></tr>
		<tr></tr>
		<tr>
			<td colspan="{{ $cols - 3 }}"></td>
			<td style="text-align: center; vertical-align: top; height: 120px;" colspan="3">
				Kepala
			</td>
		</tr>
		<tr>
			<td colspan="{{ $cols - 3 }}"></td>
			<td style="text-align: center; vertical-align: center;" colspan="3">
				Khairul Amani
			</td>
		</tr>
	</tfoot>
</table>