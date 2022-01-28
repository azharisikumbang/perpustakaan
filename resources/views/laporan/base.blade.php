<table>
	<thead>
		<tr></tr>
		<tr>
			<th 
			style="height: 30px; text-align: left; vertical-align: center; font-weight: bold; font-size: 18px" 
			colspan="{{ count($handler->getHeader()) + 1 }}">{{ strtoupper($handler->getTitle()) }}</th>
		</tr>
		<tr>
			<th>Dibuat tanggal :</th>
			<th style="text-align: left">{{ $handler->getTimestamp()->format('d/m/Y H:i:s') }}</th>
		</tr>
		<tr></tr>
		<tr>
			<th style="border: 1px solid #000; font-weight: bold; text-align: center; vertical-align: center">No</th>
			@foreach($handler->getHeader() as $header)
			<th style="border: 1px solid #000; font-weight: bold; text-align: center; vertical-align: center">{{ $header }}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($handler->getData() as $data)
		<tr>
			<th style="text-align: center; border: 1px solid #000">{{ $loop->iteration }}</th>
			@foreach($handler->getHeader() as $key => $header)
				<td style="border: 1px solid #000">{{ $data[$key] ?? '-' }}</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>