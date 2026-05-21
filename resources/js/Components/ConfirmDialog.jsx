import React from 'react';
import Button from '@/Components/Button';
import Modal from '@/Components/Modal';

export default function ConfirmDialog({
  open,
  onClose,
  onConfirm,
  title = 'Confirmar acao',
  description = 'Tem certeza que deseja continuar?',
  confirmLabel = 'Confirmar',
  cancelLabel = 'Cancelar',
  processing = false,
  tone = 'danger',
}) {
  return (
    <Modal
      open={open}
      onClose={onClose}
      title={title}
      description={description}
      size="sm"
      footer={[
        <Button key="cancel" type="button" variant="soft" onClick={onClose} disabled={processing}>
          {cancelLabel}
        </Button>,
        <Button key="confirm" type="button" variant={tone === 'danger' ? 'danger' : 'primary'} onClick={onConfirm} disabled={processing}>
          {confirmLabel}
        </Button>,
      ]}
    >
      <p className="text-sm leading-6 text-slate-600">
        Essa acao atualiza o status do registro e deve ser usada apenas quando voce realmente quiser remover o item da operacao ativa.
      </p>
    </Modal>
  );
}
