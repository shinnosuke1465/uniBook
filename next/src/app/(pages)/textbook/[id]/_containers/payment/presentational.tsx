"use client";

import { useState } from "react";
import { loadStripe } from "@stripe/stripe-js";
import {
  Elements,
  PaymentElement,
  useStripe,
  useElements,
} from "@stripe/react-stripe-js";

const stripePromise = loadStripe(
  process.env.NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY!
);

interface PaymentPresentationProps {
  clientSecret: string | null;
  error: string | null;
  price: number;
  textbookId: string;
}

export function PaymentPresentation({
  clientSecret,
  error,
  price,
  textbookId,
}: PaymentPresentationProps) {
  if (error) {
    return (
      <div className="rounded-lg bg-red-50 p-4">
        <p className="text-sm text-red-600">{error}</p>
      </div>
    );
  }

  if (!clientSecret) {
    return (
      <div className="flex items-center justify-center py-8">
        <div className="h-8 w-8 animate-spin rounded-full border-b-2 border-blue-600"></div>
        <span className="ml-3 text-gray-600">読み込み中...</span>
      </div>
    );
  }

  return (
    <Elements
      stripe={stripePromise}
      options={{
        clientSecret,
        appearance: {
          theme: "stripe",
          variables: {
            colorPrimary: "#2563eb",
          },
        },
      }}
    >
      <CheckoutForm price={price} textbookId={textbookId} />
    </Elements>
  );
}

function CheckoutForm({ price, textbookId }: { price: number; textbookId: string }) {
  const stripe = useStripe();
  const elements = useElements();
  const [isProcessing, setIsProcessing] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!stripe || !elements) {
      return;
    }

    setIsProcessing(true);
    setErrorMessage(null);

    try {
      const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
          return_url: `${window.location.origin}/textbook/${textbookId}/purchase/confirm`,
        },
      });

      if (error) {
        setErrorMessage(error.message || "決済処理に失敗しました");
        setIsProcessing(false);
      }
    } catch (err) {
      setErrorMessage("予期しないエラーが発生しました");
      setIsProcessing(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <div className="rounded-lg border border-gray-200 p-6">
        <h3 className="mb-4 text-lg font-semibold">支払い情報</h3>

        <div className="mb-4">
          <div className="flex items-center justify-between rounded-lg bg-gray-50 p-4">
            <span className="text-sm text-gray-600">支払い金額</span>
            <span className="text-2xl font-bold text-blue-600">
              ¥{price.toLocaleString()}
            </span>
          </div>
        </div>

        <PaymentElement
          options={{
            layout: "tabs",
            paymentMethodOrder: ["card"],
          }}
        />
      </div>

      {errorMessage && (
        <div className="rounded-lg bg-red-50 p-4">
          <p className="text-sm text-red-600">{errorMessage}</p>
        </div>
      )}

      <button
        type="submit"
        disabled={!stripe || isProcessing}
        className="w-full rounded-lg bg-blue-600 px-6 py-3 text-lg font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300"
      >
        {isProcessing ? "処理中..." : "購入する"}
      </button>

      <p className="text-center text-xs text-gray-500">
        支払い情報は安全に暗号化されて送信されます
      </p>
    </form>
  );
}