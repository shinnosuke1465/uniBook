import { PurchaseConfirmContainer } from "./_containers/purchase-confirm";

export default async function Page({
  params,
  searchParams,
}: {
  params: Promise<{ id: string }>;
  searchParams: Promise<{ [key: string]: string | string[] | undefined }>;
}) {
  const { id } = await params;
  const resolvedSearchParams = await searchParams;
  const paymentIntent = resolvedSearchParams.payment_intent as string | undefined;
  const paymentIntentClientSecret = resolvedSearchParams.payment_intent_client_secret as string | undefined;

  if (!paymentIntent || !paymentIntentClientSecret) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <div className="font-bold text-red-500">
          必要なパラメータが不足しています
        </div>
      </div>
    );
  }

  return (
    <PurchaseConfirmContainer
      textbookId={id}
      paymentIntentId={paymentIntent}
    />
  );
}
