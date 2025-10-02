import { PurchaseConfirmPresentation } from "./presentational";

interface PurchaseConfirmContainerProps {
  textbookId: string;
  paymentIntentId: string;
}

export async function PurchaseConfirmContainer({
  textbookId,
  paymentIntentId,
}: PurchaseConfirmContainerProps) {
  return (
    <PurchaseConfirmPresentation
      textbookId={textbookId}
      paymentIntentId={paymentIntentId}
    />
  );
}
