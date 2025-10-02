import { createPaymentIntent } from "@/services/deal/createPaymentIntent";
import { PaymentPresentation } from "./presentational";

interface PaymentContainerProps {
  textbookId: string;
  price: number;
}

export async function PaymentContainer({ textbookId, price }: PaymentContainerProps) {
  let clientSecret: string | null = null;
  let error: string | null = null;

  try {
    const paymentIntent = await createPaymentIntent(textbookId);
    clientSecret = paymentIntent.client_secret;
  } catch (err) {
    error = err instanceof Error ? err.message : "PaymentIntent作成に失敗しました";
  }

  return (
    <PaymentPresentation
      clientSecret={clientSecret}
      error={error}
      price={price}
      textbookId={textbookId}
    />
  );
}