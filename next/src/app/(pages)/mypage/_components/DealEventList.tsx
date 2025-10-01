type DealEvent = {
	id: string;
	actor_type: string;
	event_type: string;
	created_at: string | null;
};

type DealEventListProps = {
	events: DealEvent[];
};

// イベントタイプを日本語に変換
function getEventTypeLabel(eventType: string): string {
	const labels: Record<string, string> = {
		listing: "出品中",
		purchase: "購入",
		reportDelivery: "発送",
		reportReceipt: "受取報告",
		cancel: "キャンセル",
	};
	return labels[eventType] || eventType;
}

// ISO 8601形式の日時を日本語フォーマットに変換
function formatDateTime(isoString: string): string {
	const date = new Date(isoString);
	return new Intl.DateTimeFormat("ja-JP", {
		year: "numeric",
		month: "2-digit",
		day: "2-digit",
		hour: "2-digit",
		minute: "2-digit",
	}).format(date);
}

export function DealEventList({ events }: DealEventListProps) {
	return (
		<div className="bg-white rounded-lg shadow p-6">
			<h2 className="text-lg font-semibold text-gray-900 mb-4">取引履歴</h2>
			<div className="space-y-3">
				{events.map((event) => (
					<div key={event.id} className="flex items-start gap-3 text-sm">
						<span className="w-2 h-2 bg-blue-500 rounded-full mt-1.5" />
						<div className="flex-1">
							<div className="flex items-center gap-2 text-gray-900 font-medium">
								<span>
									{event.actor_type === "seller" ? "出品者" : "購入者"}
								</span>
								<span>-</span>
								<span>{getEventTypeLabel(event.event_type)}</span>
							</div>
							{event.created_at && (
								<div className="text-xs text-gray-500 mt-1">
									{formatDateTime(event.created_at)}
								</div>
							)}
						</div>
					</div>
				))}
			</div>
		</div>
	);
}